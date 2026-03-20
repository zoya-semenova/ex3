<?php B_PROLOG_INCLUDED === true || die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */
?>


<div class="ui-slider-section">
    <? if (!empty($arResult['ITEMS'])): ?>
        <? foreach ($arResult['ITEMS'] as $item): ?>
            <div class="ui-slider-content-box">
                <p class="ui-slider-paragraph"><?= $item['ID'] ?></p>
            </div>
            <div class="ui-slider-content-box">
                <p class="ui-slider-paragraph"><?= $item['TITLE'] ?></p>
            </div>
        <? endforeach; ?>
    <? endif; ?>
</div>