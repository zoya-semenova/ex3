<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

defined('B_PROLOG_INCLUDED') || die;

/**
 * @var array{additionalParameters: array} $arResult
 */

$additionalParameters = $arResult['additionalParameters'];

Extension::load('ui.hint');
?>
<tr>
    <td>
        <div id="currency-format-setting">
            <span><?= Loc::getMessage('B24_ACADEMY.UFTYPE_CURRENCY_FIELD_FORMAT_SETTING'); ?></span>
            <span data-hint-html data-hint="<?= Loc::getMessage('B24_ACADEMY.UFTYPE_CURRENCY_FIELD_FORMAT_SETTING_HINT'); ?>"></span>
        </div>
    </td>
    <td>
        <input
                type="text"
                name="<?= $additionalParameters['NAME']; ?>[FORMAT]"
                size="50"
                maxlength="255"
                value="<?= $arResult['values']['format']; ?>"
        />
    </td>
</tr>
<tr>
    <td>
        <div id="currency-link-setting">
            <span><?= Loc::getMessage('B24_ACADEMY.UFTYPE_CURRENCY_FIELD_LINK_SETTING'); ?></span>
            <span data-hint-html data-hint="<?= Loc::getMessage('B24_ACADEMY.UFTYPE_CURRENCY_FIELD_FORMAT_SETTING_HINT'); ?>"></span>
        </div>
    </td>
    <td>
        <input
                type="text"
                name="<?= $additionalParameters['NAME']; ?>[LINK]"
                size="50"
                maxlength="255"
                value="<?= $arResult['values']['link']; ?>"
        />
    </td>
</tr>
<script>
    BX.ready(function () {
        BX.UI.Hint.init();
    })
</script>