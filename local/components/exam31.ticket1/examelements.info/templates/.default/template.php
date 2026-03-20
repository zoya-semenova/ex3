<?php B_PROLOG_INCLUDED === true || die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Diag\Debug;
\Bitrix\Main\UI\Extension::load('ui.sidepanel-content');

/**
 * @var array $arParams
 * @var array $arResult
 */
?>

<?
//Debug::dump($arResult['DISPLAY_ELEMENT']);
?>
<div class="ui-slider-section">
	<? if (!empty($arResult['ITEMS'])): ?>
		<? foreach ($arResult['ITEMS'] as $value): ?>
			<div class="ui-slider-content-box">
				<p class="ui-slider-paragraph"><?= $value['TITLE'] ?></p>
			</div>
		<? endforeach; ?>
	<? endif; ?>
</div>