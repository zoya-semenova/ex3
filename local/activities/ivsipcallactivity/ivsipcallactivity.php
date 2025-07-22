<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

Loc::loadMessages(__FILE__);

/**
 * Действие "Звонок клиенту с анкетой".
 */
class CBPIvSipCallActivity extends CBPActivity implements IBPEventActivity, IBPActivityExternalEventListener
{
    /** Тип поля анкеты "заметка". Полем не является, используется добавления пояснений в анкету. */
    const FIELD_TYPE_NOTE = 'CBPIvSipCallActivity::FIELD_TYPE_NOTE';

    private $taskId = 0;
    private $taskStatus = false;

    private $isInEventActivityMode = false;

    /**
     * Инициализирует действие.
     * @param $name
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $this->arProperties = array(
            'ContactType' => 'phone',
            'Phone' => '',
            'CrmContactId' => '',
            'Responsible' => '',
            'AssignmentName' => '',
            'AssignmentDescription' => '',
            'SetStatus' => 'Y',

            // Output:
            'Comment' => '',
            'QuestionnaireResults' => array(),
            'ValueStorage' => array(),
        );

        $this->SetPropertiesTypes(array(
            'ContactType' => array('Type' => FieldType::SELECT),
            'Phone' => array('Type' => FieldType::STRING),
            'CrmContactId' => array('Type' => FieldType::INT),
            'Responsible' => array('Type' => FieldType::USER),
            'AssignmentName' => array('Type' => FieldType::STRING),
            'AssignmentDescription' => array('Type' => FieldType::TEXT),
            'SetStatus' => array('Type' => FieldType::BOOL),
            'Result' => array('Type' => FieldType::SELECT),
            'Comment' => array('Type' => FieldType::TEXT),
        ));
    }

    /**
     * Начинает выполнение действия.
     * @return int Константа CBPActivityExecutionStatus::*.
     * @throws Exception
     */
    public function Execute()
    {
        global $USER;

        if ($this->isInEventActivityMode) {
            return CBPActivityExecutionStatus::Closed;
        }

        // Создать задание и подписаться на событие завершения задания.
        $this->Subscribe($this);

        $this->isInEventActivityMode = false;

        // Задание на звонок создано, шаг еще не завершен.
        return CBPActivityExecutionStatus::Executing;
    }

    /**
     * Создает задание на звонок и подписывается на событие завершения задания (нажатие кнопки "Готово").
     * @param IBPActivityExternalEventListener $eventHandler Обработчик события завершения задания.
     * @throws Exception
     */
    public function Subscribe(IBPActivityExternalEventListener $eventHandler)
    {
        if ($eventHandler == null)
            throw new Exception('eventHandler');

        $this->isInEventActivityMode = true;

        $rootActivity = $this->GetRootActivity();
        $documentId = $rootActivity->GetDocumentId();
        $runtime = CBPRuntime::GetRuntime();
        $documentService = $runtime->GetService('DocumentService');

        // Create values for current contact.
        $questions = $this->QuestionnaireResults;
        $valueStorageName = $this->getValueStorageName();
        $valueStorage = $this->ValueStorage;
        if (empty($valueStorage[$valueStorageName])) {
            $values = array();
            foreach ($questions as $id => $question) {
                $values[$id] = $question['Default'];
            }
            $valueStorage[$valueStorageName] = $values;
        }
        $this->ValueStorage = $valueStorage;

        $arUsersTmp = $this->Responsible;
        if (!is_array($arUsersTmp)) {
            $arUsersTmp = array($arUsersTmp);
        }

        $this->WriteToTrackingService(Loc::getMessage(
            'STARTED_EXEC',
            array(
                '#ACTIVITY#' => $this->AssignmentName,
                '#USERS#' => '{=user:' . implode('}, {=user:', $arUsersTmp) . '}'
            )
        ));

        $arUsers = CBPHelper::ExtractUsers($arUsersTmp, $documentId, false);

        $arParameters = array(
            'CONTACT_TYPE' => $this->ContactType,
            'PHONE' => $this->Phone,
            'CRM_CONTACT_ID' => $this->CrmContactId,
        );

        /** @var CBPTaskService $taskService */
        $taskService = $this->workflow->GetService('TaskService');
        $this->taskId = $taskService->CreateTask(
            array(
                'USERS' => $arUsers,
                'WORKFLOW_ID' => $this->GetWorkflowInstanceId(),
                'ACTIVITY' => 'IvSipCallActivity',
                'ACTIVITY_NAME' => $this->name,
                'NAME' => $this->AssignmentName,
                'DESCRIPTION' => $this->AssignmentDescription,
                'PARAMETERS' => $arParameters,
                'IS_INLINE' => 'N',
                'DOCUMENT_NAME' => $documentService->GetDocumentName($documentId)
            )
        );

        $this->workflow->AddEventHandler($this->name, $eventHandler);
    }

    /**
     * Удаляет задание и отписывается от его событий.
     * Метод будет вызван в случае ошибки или удаления БП, чтобы корректно отменить выполнение действия.
     * @param IBPActivityExternalEventListener $eventHandler
     * @throws Exception
     */
    public function Unsubscribe(IBPActivityExternalEventListener $eventHandler)
    {
        if ($eventHandler == null)
            throw new Exception("eventHandler");

        /** @var CBPTaskService $taskService */
        $taskService = $this->workflow->GetService('TaskService');

        if ($this->taskStatus === false)
        {
            $taskService->DeleteTask($this->taskId);
        }
        else
        {
            $taskService->Update($this->taskId, array(
                'STATUS' => $this->taskStatus
            ));
        }

        $this->workflow->RemoveEventHandler($this->name, $eventHandler);

        $this->taskId = 0;
        $this->taskStatus = false;
    }

    /**
     * Метод-обработчик события завершения задания (нажатия кнопки "Готово").
     * @param array $arEventParameters
     * @throws Exception
     */
    public function OnExternalEvent($arEventParameters = array())
    {

        if ($this->executionStatus == CBPActivityExecutionStatus::Closed) {
            return;
        }

        if (!array_key_exists('USER_ID', $arEventParameters) || intval($arEventParameters['USER_ID']) <= 0) {
            return;
        }

        if (empty($arEventParameters['REAL_USER_ID'])) {
            $arEventParameters["REAL_USER_ID"] = $arEventParameters["USER_ID"];
        }

        $arUsers = CBPHelper::ExtractUsers($this->Responsible, $this->GetDocumentId(), false);

        $arEventParameters['USER_ID'] = intval($arEventParameters['USER_ID']);
        $arEventParameters['REAL_USER_ID'] = intval($arEventParameters['REAL_USER_ID']);
        if (!in_array($arEventParameters["USER_ID"], $arUsers)) {
            return;
        }

        $valueStorageName = $this->getValueStorageName();

        $allRequiredFilled = true;
        $valueStorage = $this->ValueStorage;
        foreach ($arEventParameters['QUESTIONS'] as $internalCode => $question) {
            $trimmedValue = trim($question['Value']);
            if ($question['Required'] == 'Y' && empty($trimmedValue)) {
                $allRequiredFilled = false;
            }

            $this->arProperties[$internalCode] = $question['Value'];
            $valueStorage[$valueStorageName][$internalCode] = $question['Value'];
        }
        $this->ValueStorage = $valueStorage;

        if ($allRequiredFilled) {
            $taskService = $this->workflow->GetService('TaskService');
            $taskService->MarkCompleted($this->taskId, $arEventParameters['REAL_USER_ID'], CBPTaskUserStatus::Ok);

            $this->WriteToTrackingService(Loc::getMessage('FINISHED', array('#ACTIVITY#' => $this->AssignmentName)));

            $this->taskStatus = CBPTaskStatus::CompleteOk;
            $this->Unsubscribe($this);
            $this->workflow->CloseActivity($this);
        }
    }

    /**
     * Обработчик ошибки выполнения БП (вызывается, если ошибка произошла во время выполнения данного действия).
     * @param Exception $exception
     * @return int Константа CBPActivityExecutionStatus::*.
     * @throws Exception
     */
    public function HandleFault(Exception $exception)
    {
        if ($exception == null)
            throw new Exception('exception');

        $status = $this->Cancel();
        if ($status == CBPActivityExecutionStatus::Canceling)
            return CBPActivityExecutionStatus::Faulting;

        return $status;
    }

    /**
     * Обработчик остановки БП (если остановка произошла во время выполнения данного действия).
     * @return int Константа CBPActivityExecutionStatus::*.
     * @throws Exception
     */
    public function Cancel()
    {
        if (!$this->isInEventActivityMode && $this->taskId > 0)
            $this->Unsubscribe($this);

        return CBPActivityExecutionStatus::Closed;
    }

    public function getValueStorageName()
    {
        if ($this->ContactType == 'crm_contact') {
            return 'CONTACT_' . $this->CrmContactId;
        } else {
            return 'PHONE_' . $this->Phone;
        }
    }

    /**
     * Готовит текущие настройки действия к отображению в форме настройки действия и генерирует HTML формы настройки.
     * @param array $documentType (string модуль, string класс документа, string код типа документа).
     * @param string $activityName Название действия.
     * @param array $arWorkflowTemplate Шаблон БП.
     * @param array $arWorkflowParameters Параметры шаблона БП.
     * @param array $arWorkflowVariables Переменные БП.
     * @param array|null $arCurrentValues Значения параметров действия, если есть.
     * @param string $formName
     * @return string HTML-код формы настройки шага для конструктора БП.
     */
    public static function GetPropertiesDialog($documentType, $activityName, $arWorkflowTemplate, $arWorkflowParameters, $arWorkflowVariables, $arCurrentValues = null, $formName = "")
    {
        if (!is_array($arCurrentValues))
        {
            $arCurrentValues = array(
                'ContactType' => 'phone',
                'Phone' => '',
                'CrmContactId' => '',
                'Responsible' => '',
                'AssignmentName' => '',
                'AssignmentDescription' => '',
                'SetStatus' => 'Y',
                'QuestionnaireResults' => array()
            );

            $arCurrentActivity= &CBPWorkflowTemplateLoader::FindActivityByName(
                $arWorkflowTemplate,
                $activityName
            );
            if (is_array($arCurrentActivity['Properties'])) {
                $arCurrentValues = array_merge($arCurrentValues, $arCurrentActivity['Properties']);
                $arCurrentValues['Responsible'] = CBPHelper::UsersArrayToString(
                    $arCurrentValues['Responsible'],
                    $arWorkflowTemplate,
                    $documentType
                );
            }
        }

        $runtime = CBPRuntime::GetRuntime();
        return $runtime->ExecuteResourceFile(
            __FILE__,
            'properties_dialog.php',
            array(
                'arCurrentValues' => $arCurrentValues,
                'formName' => $formName,
            )
        );
    }

    /**
     * Сохраняет настройки действия, принимает на вход данные из формы настройки действия.
     * @param array $documentType (string модуль, string класс документа, string код типа документа)
     * @param string $activityName Название действия в шаблоне БП.
     * @param array $arWorkflowTemplate Шаблон БП.
     * @param array $arWorkflowParameters Параметры шаблона БП.
     * @param array $arWorkflowVariables Переменные БП.
     * @param array $arCurrentValues Данные из формы настройки действия.
     * @param array $arErrors [Выходные данные] Ошибки валидации.
     * @return bool true, если настройки дейтсвия сохранены успешно.
     */
    public static function GetPropertiesDialogValues($documentType, $activityName, &$arWorkflowTemplate, &$arWorkflowParameters, &$arWorkflowVariables, $arCurrentValues, &$arErrors)
    {
        $arErrors = array();

        $runtime = CBPRuntime::GetRuntime();

        if (!in_array($arCurrentValues['ContactType'], array('phone', 'crm_contact'))) {
            $arErrors[] = array(
                'code' => 'Empty',
                'message' => Loc::getMessage('ERROR_INVALID_CONTACT_TYPE')
            );
        }

        if (empty($arCurrentValues['Responsible'])) {
            $arErrors[] = array(
                'code' => 'Empty',
                'message' => Loc::getMessage('ERROR_NO_RESPONSIBLE')
            );
        }

        if ($arCurrentValues['ContactType'] == 'phone') {
            if (empty($arCurrentValues['Phone'])) {
                $arErrors[] = array(
                    'code' => 'Empty',
                    'message' => Loc::getMessage('ERROR_NO_PHONE'),
                );
            }
        }

        if ($arCurrentValues['ContactType'] == 'crm_contact') {
            if (empty($arCurrentValues['CrmContactId'])) {
                $arErrors[] = array(
                    'code' => 'Empty',
                    'message' => Loc::getMessage('ERROR_NO_CRM_CONTACT'),
                );
            }
        }

        if (empty($arCurrentValues['AssignmentName'])) {
            $arErrors[] = array(
                'code' => 'Empty',
                'message' => Loc::getMessage('ERROR_NO_ASSIGN_NAME'),
            );
        }

        if (!is_array($arCurrentValues['Q'])) {
            $arErrors[] = array(
                'code' => 'Empty',
                'message' => Loc::getMessage('ERROR_NO_QUESTIONS'),
            );
        }

        $arQuestions = array();
        $noteIndex = 0;
        foreach ($arCurrentValues['Q'] as $question) {
            $fieldTypes = array(
                FieldType::STRING,
                FieldType::DOUBLE,
                FieldType::TEXT,
                FieldType::SELECT,
                self::FIELD_TYPE_NOTE
            );
            if (!in_array($question['Type'], $fieldTypes)) {
                $arErrors[] = array(
                    'code' => 'Empty',
                    'message' => Loc::getMessage('ERROR_INVALID_Q_TYPE')
                );
            }

            if ($question['Type'] == self::FIELD_TYPE_NOTE) {
                if (empty($question['Note'])) {
                    $arErrors[] = array(
                        'code' => 'Empty',
                        'message' => Loc::getMessage('ERROR_NO_Q_NOTE')
                    );
                }
            } else {
                if (!preg_match('/^[0-9a-z_]+$/i', $question['Code'])) {
                    $arErrors[] = array(
                        'code' => 'InvalidValue',
                        'message' => Loc::getMessage('ERROR_INVALID_CODE')
                    );
                }

                if (empty($question['Name'])) {
                    $arErrors[] = array(
                        'code' => 'Empty',
                        'message' => Loc::getMessage('ERROR_NO_Q_NAME')
                    );
                }

                if ($question['Type'] == FieldType::SELECT) {
                    if (!is_array($question['Options'])) {
                        $arErrors[] = array(
                            'code' => 'Empty',
                            'message' => Loc::getMessage('ERROR_NO_Q_OPTIONS')
                        );
                    } else {
                        $effectiveOptions = array();
                        foreach ($question['Options'] as $option) {
                            $trimmed = trim($option);
                            if (!empty($trimmed)) {
                                $effectiveOptions[] = $trimmed;
                            }
                        }
                        if (empty($effectiveOptions)) {
                            $arErrors[] = array(
                                'code' => 'Empty',
                                'message' => Loc::getMessage('ERROR_NO_Q_OPTIONS')
                            );
                        } else {
                            $question['Options'] = $effectiveOptions;
                        }
                    }
                }
            }

            $questionCode = $question['Type'] == self::FIELD_TYPE_NOTE ? $noteIndex++ : $question['Code'];
            $arQuestions['Q_' . $questionCode] = array(
                'Note' => $question['Note'],
                'Code' => $question['Code'],
                'Name' => $question['Name'],
                'Required' => $question['Required'] == 'Y' ? 'Y' : 'N',
                'Type' => $question['Type'],
                'Options' => $question['Options'],
                'Default' => strval($question['Default']),
            );
        }

        if (!empty($arErrors)) {
            return false;
        }

        $arProperties = array(
            'ContactType' => $arCurrentValues['ContactType'],
            'Phone' => $arCurrentValues['Phone'],
            'CrmContactId' => $arCurrentValues['CrmContactId'],
            'Responsible' => CBPHelper::UsersStringToArray(
                $arCurrentValues['Responsible'],
                $documentType,
                $arErrors
            ),
            'AssignmentName' => $arCurrentValues['AssignmentName'],
            'AssignmentDescription' => $arCurrentValues['AssignmentDescription'],
            'SetStatus' => $arCurrentValues['SetStatus'],
            'QuestionnaireResults' => $arQuestions,
        );

        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName(
            $arWorkflowTemplate,
            $activityName
        );
        $arCurrentActivity['Properties'] = $arProperties;

        return true;
    }

    /**
     *
     * @param array $arTask Поля задания.
     * @param int $userId
     * @param string $userName
     * @return array (string HTML-код формы задания, string HTML-код кнопок под формой: "готово" и т. п.).
     * @throws Exception
     * @throws \Bitrix\Main\LoaderException
     */
    public static function ShowTaskForm($arTask, $userId, $userName = "")
    {
        if ($arTask['PARAMETERS']['CONTACT_TYPE'] == 'phone') {
            $phones = array(
                array(
                    'VALUE' => $arTask['PARAMETERS']['PHONE'],
                    'VALUE_TYPE' => 'WORK'
                )
            );
            $contactHtml = self::getSipPhoneHtml($phones);
        } else if ($arTask['PARAMETERS']['CONTACT_TYPE'] == 'crm_contact' && Loader::includeModule('crm')) {
            $arContact = CCrmContact::GetByID($arTask['PARAMETERS']['CRM_CONTACT_ID']);
            if (!empty($arContact)) {
                $arFm = CCrmFieldMulti::GetEntityFields('CONTACT', $arContact['ID'], 'PHONE');
                $contactHtml = self::getContactHtml($arContact, $arFm);
            } else {
                $contactHtml = Loc::getMessage('ERROR_CONTACT_NOT_FOUND');
            }
        } else {
            $contactHtml = '(error)';
        }

        $runtime = CBPRuntime::GetRuntime();
        $workflow = $runtime->GetWorkflow($arTask['WORKFLOW_ID'], true);

        /** @var CBPIvSipCallActivity $activity */
        $activity = $workflow->GetActivityByName($arTask['ACTIVITY_NAME']);

        $questions = $activity->QuestionnaireResults;
        $valueStorageName = $activity->getValueStorageName();
        $values = $activity->ValueStorage[$valueStorageName];
        foreach ($questions as $id => $question) {
            $questions[$id]['Value'] = $values[$id];
        }

        $form = $runtime->ExecuteResourceFile(
            __FILE__,
            'tasktemplate.php',
            array(
                'arResult' => array(
                    'CONTACT_HTML' => $contactHtml,
                    'QUESTIONS' => $questions
                ),
            )
        );

        $buttons = '<input type="submit" name="finish" value="' . Loc::getMessage('FINISH') . '">';

        return array($form, $buttons); // form, buttons
    }

    /**
     * @param array $arTask Поля задания.
     * @return array
     */
    public static function getTaskControls($arTask)
    {
        return array(
            'BUTTONS' => array(
                array(
                    'TYPE'  => 'submit',
                    'TARGET_USER_STATUS' => CBPTaskUserStatus::Ok,
                    'NAME'  => 'finish',
                    'VALUE' => 'Y',
                    'TEXT'  => Loc::getMessage('FINISH')
                )
            )
        );
    }

    /**
     * Обрабатывает отправку формы задания (когда пользователь нажмет "Готово").
     * @param array $arTask Поля задания
     * @param int $userId
     * @param array $arRequest $_REQUEST
     * @param array $arErrors [Выходные данные] Ошибки валидации.
     * @param string $userName
     * @param int $realUserId
     * @return bool true, если нет ошибок валидации.
     */
    public static function PostTaskForm($arTask, $userId, $arRequest, &$arErrors, $userName = '', $realUserId = null)
    {
        $arErrors = array();
        
        try {
            $userId = intval($userId);
            if ($userId <= 0) {
                throw new CBPArgumentNullException('userId');
            }

            $runtime = CBPRuntime::GetRuntime();
            $workflow = $runtime->GetWorkflow($arTask['WORKFLOW_ID'], true);
            $activity = $workflow->GetActivityByName($arTask['ACTIVITY_NAME']);

            $questions = $activity->QuestionnaireResults;
            foreach ($questions as $internalCode => $question) {
                $questions[$internalCode]['Value'] = $arRequest[$question['Code']];
            }

            $arEventParameters = array(
                'USER_ID' => $userId,
                'REAL_USER_ID' => $realUserId,
                'USER_NAME' => $userName,
                'QUESTIONS' => $questions,
            );

            CBPRuntime::SendExternalEvent($arTask['WORKFLOW_ID'], $arTask['ACTIVITY_NAME'], $arEventParameters);

            return true;
            
        } catch (Exception $e) {
            $arErrors[] = array(
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'file' => $e->getFile().' ['.$e->getLine().']',
            );
        }

        return false;
    }

    /**
     * Генерирует HTML-код виджета для совершения звонка через телефонию для произвольного номера телефона.
     * @param array[] $phones array(array('VALUE' => '+12345678901', 'VALUE_TYPE' => '<phone type>'), ...),
     *  See CCrmFieldMulti::GetEntityTypes() return for phone types.
     * @param int $crmContactId
     * @return string HTML.
     */
    private static function getSipPhoneHtml($phones, $crmContactId = null)
    {
        Loader::includeModule('crm');

        $asset = Asset::getInstance();
        $asset->addCss('/bitrix/js/crm/css/crm.css');
        $asset->addJs('/bitrix/js/crm/common.js');

        $typeID = 'PHONE';

        $arEntityFields = array(
            'FM' => array(
                $typeID => $phones
            )
        );

        $arOptions = array(
            'ENABLE_SIP' => true,
            'SIP_PARAMS' => array()
        );

        if (intval($crmContactId) > 0) {
            $arOptions['SIP_PARAMS']['ENTITY_TYPE'] = 'CRM_CONTACT';
            $arOptions['SIP_PARAMS']['ENTITY_ID'] = intval($crmContactId);
        }

        return CCrmViewHelper::PrepareFormMultiField(
            $arEntityFields,
            $typeID,
            '',
            null,
            $arOptions
        );
    }

    /**
     * Генерирует HTML-код виджета для совершения звонка через телефонию для контакта CRM.
     * @param array $arContact
     * @param array $multiValues Should contain TYPE_ID=PHONE fields.
     * @return string HTML.
     */
    private static function getContactHtml($arContact, $multiValues)
    {
        $photoHtml = '';
        if (!empty($arContact['PHOTO'])) {
            $arResizedPhoto = CFile::ResizeImageGet(
                $arContact['PHOTO'],
                array('width' => 38, 'height' => 38),
                BX_RESIZE_IMAGE_EXACT
            );
            $photoHtml = '<img src="' . $arResizedPhoto['src'] . '">';
        }

        $phones = array();
        foreach ($multiValues as $value) {
            if ($value['TYPE_ID'] != 'PHONE') {
                continue;
            }

            $phones[] = array(
                'VALUE' => $value['VALUE'],
                'VALUE_TYPE' => $value['VALUE_TYPE']
            );
        }
        $sipHtml = self::getSipPhoneHtml($phones, $arContact['ID']);


        $showPath = CComponentEngine::MakePathFromTemplate(
            Option::get('crm', 'path_to_contact_show'),
            array('contact_id' => $arContact['ID'])
        );

        $html = '';

        $html .= '<a class="crm-detail-info-resp crm-detail-info-head-cont" ' .
            'id="crm_deal_show_v12_qpv_secondary_client_container" target="_blank" href="' . $showPath . '">';
        $html .= '<div class="crm-detail-info-resp-img">' . $photoHtml . '</div>';
        $html .= '<span class="crm-detail-info-resp-name">' . $arContact['FULL_NAME'] . '</span>';
        $html .= '<span class="crm-detail-info-resp-descr">' . $arContact['POST'] . '</span>';
        $html .= '</a>';

        $html .= '<div class="crm-detail-info-item">';
        $html .= '<span class="crm-detail-info-item-name">' . Loc::getMessage('PHONE') . ':</span>';
        $html .= '<span class="crm-client-contacts-block-text crm-client-contacts-block-handset" style="max-width: 100%; width: 324px;">';
        $html .= $sipHtml;
        $html .= '</span></div>';

        return $html;
    }
}