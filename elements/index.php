<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Уведомления");
?>
<?php
$APPLICATION->IncludeComponent(
	"local.ex31:elements",
	"",
	Array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"SEF_FOLDER" => "/elements/",
		"SEF_MODE" => "Y",
		"SEF_URL_TEMPLATES" => Array("detail" => "#ID#/","list" => "")
	)
);
?>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>