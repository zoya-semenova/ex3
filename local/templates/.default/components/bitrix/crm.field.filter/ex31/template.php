<?php

use Local\Ex31\UserField\Ex31Field;

defined('B_PROLOG_INCLUDED') || die;

/**
 * @var array{arUserField: array} $arParams
 * @var array{VALUE: array|string} $arResult
 */

print Ex31Field::getFilterHtml($arParams['arUserField'], ['VALUE' => (array) $arResult['VALUE']]);