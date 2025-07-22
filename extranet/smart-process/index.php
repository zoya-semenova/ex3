<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Смарт-процессы");
?>
<?
//print_r(SITE_DIR);exit;
/** @var CMain $APPLICATION */

//$_SERVER['REQUEST_URI'] = str_replace('/extranet/crm/', '/crm/', $_SERVER['REQUEST_URI']);
//define(SITE_DIR,   '/');
/*
$APPLICATION->includeComponent(
    'bitrix:crm.router',
    '',
    [
        'root' => '/extranet/crm/',
        'isExternal' => true,
    ]
);
*/

CModule::IncludeModule('crm');
use Bitrix\Crm\Service\Container;

$APPLICATION->IncludeComponent('bitrix:crm.item.'.$_REQUEST['component'],
    '',
    [
        'entityTypeId' => $_REQUEST['type'],
        'categoryId' => $_REQUEST['category'],

//        'parentEntityTypeId' => $parentEntityTypeId,
//        'parentEntityId' => $parentEntityId,
//        'backendUrl' => Container::getInstance()->getRouter()->getChildrenItemsListUrl(
//            $entityTypeId,
//            $parentEntityTypeId,
//            $parentEntityId
//        ),
//        'isEmbedded' => true,
    ] ,

    false,
    [
        'HIDE_ICONS' => 'Y',
        'ACTIVE_COMPONENT' => 'Y',
    ]
);


/*
CModule::IncludeModule('crm');
use Bitrix\Crm\Service\Container;

$factory = \Bitrix\Crm\Service\Container::getInstance()->getFactory(1036);
//echo $USER->getId();exit;
echo "<pre>";
$list = $factory->getItemsFilteredByPermissions(
    [
    ],
    4,
    //$USER->getId(),
   // 1,
    \Bitrix\Crm\Service\UserPermissions::OPERATION_READ
);

foreach($list as $item)
{
    //echo $item->getId();exit;
    $itemData = $item->getData();


    echo "<pre>";print_r($itemData);exit;


    $itemData = array_diff_key($itemData, $notAccessibleFields);
    $itemsData[$itemData['ID']] = $itemData;

    if ($isExportEventEnabled && $this->isExportMode())
    {
        $trackedObject = $this->factory->getTrackedObject($item);
        Container::getInstance()->getEventHistory()->registerExport($trackedObject);
    }
    $listById[$item->getId()] = $item;
}

echo "<pre>";print_r($list);//exit;
*/
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>