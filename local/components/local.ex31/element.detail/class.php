<?php


use Local\Ex31\History\Service as HistoryService;
use Local\Ex31\Integration\Intranet\Employee\Service as EmployeeService;
use Local\Ex31\Integration\UI\EntityEditor\ProjectProvider;
use Local\Ex31\Integration\UI\FieldNameProvider;
use Local\Ex31\Element;
use Local\Ex31\Service as ProjectService;
use Local\Ex31\ServiceException;
use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Diag\ExceptionHandler;
use Bitrix\Main\Diag\ExceptionHandlerLog;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\Error as BitrixError;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorableImplementation;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectException;
use Bitrix\Main\Type\DateTime as BitrixDateTime;
use Bitrix\UI\Buttons\Button;
use Bitrix\UI\Buttons\Color;
use Bitrix\UI\Buttons\Icon;

final class ElementDetailComponent extends CBitrixComponent implements Errorable, Controllerable
{
    use ErrorableImplementation;

    private readonly ProjectService $projectService;
    private readonly FieldNameProvider $fieldNameProvider;
    private readonly ExceptionHandler $exceptionHandler;

    private ?int $entityId = null;

    /**
     * @throws LoaderException
     */
    public function __construct(
        ?CBitrixComponent $component = null,
        ?ProjectService $projectService = null,
        ?FieldNameProvider $fieldNameProvider = null,
        ?ExceptionHandler $exceptionHandler = null
    ) {
        parent::__construct($component);

        Loader::requireModule('local.ex31');

        $this->projectService = $projectService ?? new ProjectService(
            new HistoryService(),
            CurrentUser::get()
        );
        $this->fieldNameProvider = $fieldNameProvider ?? new FieldNameProvider();
        $this->exceptionHandler = $exceptionHandler ?? Application::getInstance()->getExceptionHandler();

        $this->errorCollection = new ErrorCollection();
    }

    public function onPrepareComponentParams($arParams): array
    {
        if (!isset($arParams['INVESTMENT_PROJECT_ID'])) {
            $this->errorCollection->setError(
                new BitrixError(Loc::getMessage('INVESTMENT_PROJECT_NOT_FOUND_ERROR'))
            );
            return $arParams;
        }

        $projectId = (int)$arParams['INVESTMENT_PROJECT_ID'];
        if ($projectId < 0) {
            $this->errorCollection->setError(
                new BitrixError(Loc::getMessage('INVESTMENT_PROJECT_NOT_FOUND_ERROR'))
            );
            return $arParams;
        }

        $this->entityId = $projectId;
        return $arParams;
    }

    public function executeComponent(): void
    {
        if ($this->hasErrors()) {
            $this->displayErrors();
            return;
        }

        $item = !empty($this->entityId) ? $this->projectService->getById($this->entityId) : null;
        $provider = new ProjectProvider($item, $this->fieldNameProvider);

        $toolbarButtons = [];
        if (isset($item)) {
            $toolbarButtons[] = new Button([
                'text' => Loc::getMessage('INVESTMENT_PROJECT_HISTORY_BUTTON_LABEL'),
                'link' => CComponentEngine::makePathFromTemplate(
                    $this->arParams['HISTORY_PAGE_URL'],
                    ['INVESTMENT_PROJECT_ID' => $item->id]
                ),
                'icon' => Icon::LIST,
                'color' => Color::LIGHT_BORDER
            ]);
        }

        $this->arResult = [
            'form' => array_merge(
                $provider->getFields(),
                [
                    'COMPONENT_AJAX_DATA' => [
                        'COMPONENT_NAME' => $this->getName(),
                        'SIGNED_PARAMETERS' => $this->getSignedParameters()
                    ],
                    'ENABLE_CONFIG_CONTROL' => false
                ]
            ),
            'title' => $provider->getEntityTitle(),
            'toolbar' => [
                'buttons' => $toolbarButtons
            ]
        ];
        $this->includeComponentTemplate();
    }

    private function displayErrors(): void
    {
        foreach ($this->getErrors() as $error) {
            ShowError($error->getMessage());
        }
    }

    public function saveAction(array $data): AjaxJson
    {
//        if (isset($data['ACTIVE'])) {
//            $data['ACTIVE'] = ($data['ACTIVE'] == 'Y');
//        }
        try {
            if (!empty($this->arParams['INVESTMENT_PROJECT_ID'])) {
                $item = $this->projectService->getById((int)$this->arParams['INVESTMENT_PROJECT_ID']);
                $item->title = $data['TITLE'] ?? $item->title;
                $item->text = $data['TEXT'] ?? $item->text;
                $item->active = $data['ACTIVE'] ?? $item->active;

                $this->projectService->update($item);
            } else {
                $item = new Element(
                    $data['ID'] ?? null,
                    $data['TITLE'],
                    null,
                    $data['ACTIVE'],
                    $data['TEXT']
                );

                $item = $this->projectService->create($item);
            }

            return AjaxJson::createSuccess([
                'ENTITY_ID' => $item->id,
                'REDIRECT_URL' => CComponentEngine::makePathFromTemplate(
                    $this->arParams['DETAIL_PAGE_URL'],
                    ['INVESTMENT_PROJECT_ID' => $item->id]
                )
            ]);
        } catch (ServiceException $e) {//echo "<pre>";print_r($e->getMessage());echo "</pre>";exit;
            $this->errorCollection->setError(
                new BitrixError(
                    Loc::getMessage('INVESTMENT_PROJECT_PROCESS_PROJECT_ERROR')
                )
            );
            $this->exceptionHandler->writeToLog($e, ExceptionHandlerLog::CAUGHT_EXCEPTION);
            return AjaxJson::createError($this->errorCollection);
        }
    }

    public function configureActions(): array
    {
        return [];
    }

    protected function listKeysSignedParameters(): array
    {
        return ['INVESTMENT_PROJECT_ID', 'DETAIL_PAGE_URL'];
    }
}