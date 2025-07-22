<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class CBPMyActivity
    extends CBPActivity
{
    public function __construct($name)
    {
        parent::__construct($name);
        // Определим свойство действия MyText
        // Оно может быть задано в визуальном редакторе при
        // помещении действия в шаблон бизнес-процесса
        $this->arProperties = array("Title" => "", "MyText" => "");
    }

    // Исполняющийся метод действия
    public function Execute()
    {
        // Суть действия – запись значения свойства в файл
        if (strlen($this->MyText) > 0)
        {
            $f = fopen($_SERVER["DOCUMENT_ROOT"]."/dump.txt", "a");
            fwrite($f, $this->MyText);
            fclose($f);
        }

        // Возвратим исполняющей системе указание, что действие завершено
        return CBPActivityExecutionStatus::Closed;
    }

    // Статический метод возвращает HTML-код диалога настройки
// свойств действия в визуальном редакторе. Если действие не имеет
// свойств, то этот метод не нужен
    public static function GetPropertiesDialog($documentType, $activityName,
                                               $arWorkflowTemplate,$arWorkflowParameters, $arWorkflowVariables,
                                               $arCurrentValues = null, $formName = "")
    {
        $runtime = CBPRuntime::GetRuntime();

        if (!is_array($arWorkflowParameters))
            $arWorkflowParameters = array();
        if (!is_array($arWorkflowVariables))
            $arWorkflowVariables = array();

        // Если диалог открывается первый раз, то подгружаем значение
        // свойства, которое было сохранено в шаблоне бизнес-процесса
        if (!is_array($arCurrentValues))
        {
            $arCurrentValues = array("my_text" => "");

            $arCurrentActivity= &CBPWorkflowTemplateLoader::FindActivityByName(
                $arWorkflowTemplate,
                $activityName
            );
            if (is_array($arCurrentActivity["Properties"]))
                $arCurrentValues["my_text "] =
                    $arCurrentActivity["Properties"]["MyText"];
        }

        // Код, формирующий диалог, расположен в отдельном файле
        // properties_dialog.php в папке действия.
        // Возвращаем этот код.
        return $runtime->ExecuteResourceFile(
            __FILE__,
            "properties_dialog.php",
            array(
                "arCurrentValues" => $arCurrentValues,
                "formName" => $formName,
            )
        );
    }

    // Статический метод получает введенные в диалоге настройки свойств
// значения и сохраняет их в шаблоне бизнес-процесса. Если действие не
// имеет свойств, то этот метод не нужен.
    public static function GetPropertiesDialogValues($documentType, $activityName,
                                                     &$arWorkflowTemplate, &$arWorkflowParameters, &$arWorkflowVariables,
                                                     $arCurrentValues, &$arErrors)
    {
        $arErrors = array();

        $runtime = CBPRuntime::GetRuntime();

        if (strlen($arCurrentValues["my_text "]) <= 0)
        {
            $arErrors[] = array(
                "code" => "emptyCode",
                "message" => GetMessage("MYACTIVITY_EMPTY_TEXT"),
            );
            return false;
        }

        $arProperties = array("MyText" => $arCurrentValues["my_text "]);

        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName(
            $arWorkflowTemplate,
            $activityName
        );
        $arCurrentActivity["Properties"] = $arProperties;

        return true;
    }
}
?>