<?php

defined('B_PROLOG_INCLUDED') || die;

/**
 * @var array $arResult
 */

$values = [];
if (isset($arResult['additionalParameters']['bVarsFromForm']) && $arResult['additionalParameters']['bVarsFromForm']) {
    $values['format'] = $GLOBALS[$arResult['additionalParameters']['NAME']]['FORMAT'] ?? '';
} elseif (isset($arResult['userField']) && $arResult['userField']) {
    $values['format'] = $arResult['userField']['SETTINGS']['FORMAT'];
} else {
    $values['format'] = 'Элемент [#ID#] - #TITLE#';
}

if (isset($arResult['additionalParameters']['bVarsFromForm']) && $arResult['additionalParameters']['bVarsFromForm']) {
    $values['link'] = $GLOBALS[$arResult['additionalParameters']['NAME']]['LINK'] ?? '';
} elseif (isset($arResult['userField']) && $arResult['userField']) {
    $values['link'] = $arResult['userField']['SETTINGS']['LINK'];
} else {
    $values['link'] = '/invest/info/#ID#/';
}

$arResult['values'] = $values;