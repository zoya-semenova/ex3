<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/extranet/public/contacts/index.php");

$APPLICATION->SetTitle(GetMessage("EXTRANET_CONTACTS_LIST"));
?>
<?php
$componentParams = [
	"LIST_URL" => SITE_DIR . "contacts/",
];

$APPLICATION->IncludeComponent(
	"bitrix:ui.sidepanel.wrapper",
	"",
	array(
		'POPUP_COMPONENT_NAME' => "bitrix:intranet.user.list",
		"POPUP_COMPONENT_TEMPLATE_NAME" => "",
		"POPUP_COMPONENT_PARAMS" => $componentParams,

		"USE_UI_TOOLBAR" => "Y",

		'USE_PADDING' => 1,
		'PLAIN_VIEW' => 1,
		'USE_UI_TOOLBAR' => 'Y',
		'POPUP_COMPONENT_USE_BITRIX24_THEME' => 'N',
		'DEFAULT_THEME_ID' => '',
		'USE_BACKGROUND_CONTENT' => 1,
		'HIDE_TOOLBAR' => '',
		'PREVENT_LOADING_WITHOUT_IFRAME' => 1
	)
);
?>
<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
