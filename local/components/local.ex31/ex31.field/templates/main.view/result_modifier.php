<?php

defined('B_PROLOG_INCLUDED') || die;

/**
 * @var CBitrixComponentTemplate $this
 * @var CurrencyFieldComponent $component
 * @var array{userField: array{MULTIPLE: string, SETTINGS: array{FORMAT: string}, value: string|array} $arResult
 */

$component = $this->getComponent();
$value = (array)$arResult['value'] ?? [];

$arResult['formattedValue'] = [];
foreach ($value as $currencyId) {
    $arResult['formattedValue'][$currencyId] = $component->formatLink($currencyId) ?
        '<a href='.$component->formatLink($currencyId).'>'.$component->formatCurrency($currencyId).'</a>':
        $component->formatCurrency($currencyId);
}