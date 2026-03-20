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

$jsonData = \Bitrix\Main\Web\Json::encode([
    'messages' => Bitrix\Main\Localization\Loc::loadLanguageFile(__FILE__),
    'gridManager' => $arResult['gridManager']
]);
?>
<script>
    BX.ready(() => {
        const data = <?= $jsonData; ?>;

        const grid = BX.Main.gridManager.getInstanceById(data.gridManager.gridId);
        if (!grid) {
            throw `Grid with id ${gridId} not found`;
        }
      //  BX.message(data.messages);
      //  BX.Academy.Element.Grid.Manager.initialize(data.gridManager);
    })
</script>