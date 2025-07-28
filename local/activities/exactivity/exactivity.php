<?php


use Bitrix\Bizproc\Activity\BaseActivity;
use Bitrix\Bizproc\Activity\PropertiesDialog;
use Bitrix\Bizproc\FieldType;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Query\Query;
use Local\Ex31\Collection;
use Local\Ex31\ElementTable;

defined('B_PROLOG_INCLUDED') || die;

/**
 * @property-write array $ObservedFields
 * @property-write int $ExpiresIn
 * @property-write array $UpdatedFields
 * @property-write bool $IsTimeout
 */
class CBPExActivity extends BaseActivity
{
    private int $ex31Id;
    private array $documentSnapshot = [];

    public function __construct($name)
    {
        parent::__construct($name);

        $this->arProperties = [
            'Fields' => [],
            'ObservedFields' => ['ggg'],
            'ElId' => 0,
        ];

        $this->setPropertiesTypes([
            'Fields' => [
                'Name' => 'Fields',
                'Type' => FieldType::SELECT,
                'Multiple' => true
            ],
            'ElId' => [
                'Type' => FieldType::INT
            ]
        ]);
    }

    protected function prepareProperties(): void
    {
        parent::prepareProperties();

        $options = [];


//            $service = $this->workflow
//                ->getRuntime()
//                ->getDocumentService();
//            $type = $service->getDocumentType();
//            $fields = $service->getDocumentFields($type);

      //  $d = \Local\Ex31\ElementTable::createObject();
       // $d = \Local\Ex31\ElementTable::getF

        $fields = [
            1=>[
                'Name' => '111'
            ]
        ];

            foreach ($fields as $id => $field) {
                $options[$id] = $field['Name'];
            }


        $this->preparedProperties['ObservedFields'] = [
            [
                "NAME" => "Список задач",
                "TYPE" => "string",
            ]
        ];

        $this->preparedProperties['Fields'] = [
            'field11' => [
                "Name" => "Список задач",
                "Type" => "string",
            ]
        ];
    }

    protected static function getFileName(): string
    {
        return __FILE__;
    }

    public function internalExecute(): ErrorCollection
    {
        $collection = parent::internalExecute();

        $this->documentSnapshot = $this->workflow
            ->getRuntime()
            ->getDocumentService()
            ->getDocument($this->getDocumentId());


        return $collection;
    }
    public function execute(): int
    {

        \Bitrix\Main\Loader::requireModule('local.ex31');

        if ($this->ElId) {
            $result =  (new Query(
                ElementTable::getEntity()
            ))
                ->setSelect([
                    'ID',
                    'TITLE',
                    'MODIFY_DATE',
                    'ACTIVE',
                    'TEXT',
                    // 'INFO_ID' => 'INFO.ID',
                    // 'cnt'
                    // 'BOOKS.ID',
                    // 'BOOKS.COUNT',
                    //  'BOOKS.ID',
                    //  'BOOKS1'
                ])
                ->whereIn('ID', [$this->ElId])
                //  ->setOrder($order);
                //->setLimit($limit)
                // ->setOffset($offset);
                //->exec();
            ;

            // echo "<pre>";
            //  print_r( $result->getQuery());

            $r = $result->exec();
            // print_r($r->get)
            //  print_r($result->fetchAll());exit;
            $projects = new Collection();
            foreach ($r->fetchAll() as $entityArr) {
                $this->WriteToTrackingService(json_encode($entityArr));
            }

            $this->arProperties['field4'] = $entityArr['TITLE'];
        }

        $this->Fields['field1'] = '22';
        $this->arProperties['field1'] = '33';
        $this->arProperties['field3'] = '44';

        //$this->fieldsMap = ['field1'];

        //$this->QuestionnaireResults['111'] = '123';

        $this->WriteToTrackingService(json_encode($this->documentSnapshot));

        $this->WriteToTrackingService(json_encode($this->ElId));

        return CBPActivityExecutionStatus::Closed;
    }

    public static function getPropertiesDialog($documentType, $activityName, $arWorkflowTemplate,
                                               $arWorkflowParameters, $arWorkflowVariables,
                                               $arCurrentValues = null, $formName = "",
                                                                        $popupWindow = null,
                                                                        $siteId = '')
    {
      //  if (!is_array($arCurrentValues))
     //   {
            $arCurrentValues = array(
                'ElId' => 0,
                'Fields' => array()
            );


            $arCurrentActivity= &CBPWorkflowTemplateLoader::FindActivityByName(
                $arWorkflowTemplate,
                $activityName
            );
            if (is_array($arCurrentActivity['Properties'])) {
                $arCurrentValues = array_merge($arCurrentValues, $arCurrentActivity['Properties']);

            }
       // }

        $arCurrentValues['Fields'] = [
            'field1' => array(
                'Note' => 'field1',
                'Code' => 'field1',
                'Name' => 'field1',
                'Type' => 'string',
            ),
            'field2' => array(
                'Note' => 'field2',
                'Code' => 'field2',
                'Name' => 'field2',
                'Type' => 'string',
            )
        ];

        $currentValues['Fields'] = $arCurrentActivity['Properties']['Fields'];

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
    public static function getPropertiesDialogMap(?PropertiesDialog $dialog = null): array
    {

        return [
            'ElId' => [
                'Type' => FieldType::INT,
                'Name' => 'ElId',
                'FieldName' => 'ElId',
                'Multiple' => false,
                'Required' => false
            ],
            'Fields' => [
                'Type' => FieldType::SELECT,
                'Name' => 'Fields',
                'FieldName' => 'Fields',
                'Multiple' => true,
                'Required' => false
            ],
        ];
    }

    public static function getPropertiesDialogValues($documentType, $activityName, &$arWorkflowTemplate,
                                                     &$arWorkflowParameters,
                                                     &$arWorkflowVariables, $arCurrentValues, &$arErrors): bool
    {
        $arErrors = array();

        $runtime = CBPRuntime::GetRuntime();



       // foreach ($arCurrentValues['Fields'] as $key => $value) {
            $arFields['field1'] = array(
                'Note' => 'field1',
                'Code' => 'field1',
                'Name' => 'field1',
                'Required' =>  'Y',
                'Type' => 'string',
            );
        $arFields['field2'] = array(
            'Note' => 'field2',
            'Code' => 'field2',
            'Name' => 'field2',
            'Required' =>  'Y',
            'Type' => 'string',
        );
        $arFields['field3'] = array(
            'Note' => 'field3',
            'Code' => 'field3',
            'Name' => 'field3',
            'Required' =>  'Y',
            'Type' => 'string',
        );

        $arFields['field4'] = [
        'Type' => FieldType::STRING,
        'Name' => 'Поле 4',
        'Required' => false
    ];
      //  }


        $arProperties = array(

            'Fields' => $arFields,
            "ElId" => $arCurrentValues["ElId"],
        );

        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName(
            $arWorkflowTemplate,
            $activityName
        );
        $arCurrentActivity['Properties'] = $arProperties;

        return true;
    }
}