<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("111");
?>
<?
//print_r(SITE_DIR);exit;
/** @var CMain $APPLICATION */

$APPLICATION->IncludeComponent(
    "bitrix:ui.sidepanel.wrapper",
    "",
    [
        'POPUP_COMPONENT_NAME' => "bitrix:crm.item.details",
        "POPUP_COMPONENT_TEMPLATE_NAME" => "",
        "POPUP_COMPONENT_PARAMS" => [
            'ENTITY_TYPE_ID' => $_REQUEST['type'],
            'ENTITY_ID' => $_REQUEST["id"],
            'isExternal' => true, 'IFRAME_TYPE' => ''
        ],
        'USE_PADDING' => 1,
        'PLAIN_VIEW' => 1,
        'USE_UI_TOOLBAR' => 'Y',
        'POPUP_COMPONENT_USE_BITRIX24_THEME' => 'N',
        'DEFAULT_THEME_ID' => '',
        'USE_BACKGROUND_CONTENT' => 1,
        'HIDE_TOOLBAR' => '',]
);
/*
$APPLICATION->includeComponent(
    'bitrix:crm.item.details',
    '',
    [
        'ENTITY_TYPE_ID' => '1036',
        'ENTITY_ID' => 2,
        'isExternal' => true,
//        'IFRAME' => 'N',
//        'IFRAME_TYPE'=>''
    ]
);
*/
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
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
