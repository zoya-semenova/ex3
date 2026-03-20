<?php B_PROLOG_INCLUDED === true || die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
?>


<?
foreach ($arResult['toolbar']['buttons'] as $button) {
    \Bitrix\UI\Toolbar\Facade\Toolbar::addButton($button);
}

\Bitrix\UI\Toolbar\Facade\Toolbar::addFilter($arResult['filter']);

$APPLICATION->IncludeComponent(
	'bitrix:main.ui.grid',
	'',
	$arResult["grid"],
	$component
);
?>