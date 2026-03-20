<?php
defined('B_PROLOG_INCLUDED') || die;
?>

<?
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/local/modules/exam31.ticket/lib/examfieldtype.php');
$additionalParameters = $arResult['additionalParameters'];
?>

<tr>
    <td>
        <div id="currency-format-setting">
            <span><?=GetMessage('EXAM31_TICKET_FIELDTYPE_UF_VALUE_FORMAT')?></span>
        </div>
    </td>
    <td>
        <input
                type="text"
                name="<?= $additionalParameters['NAME']; ?>[FORMAT]"
                size="50"
                maxlength="255"
                value="<?= $arResult['VALUES']['FORMAT']; ?>"
        />
    </td>
</tr>
<tr>
    <td>
        <div id="currency-link-setting">
            <span><?=GetMessage('EXAM31_TICKET_FIELDTYPE_UF_VALUE_LINK')?></span>
        </div>
    </td>
    <td>
        <input
                type="text"
                name="<?= $additionalParameters['NAME']; ?>[LINK]"
                size="50"
                maxlength="255"
                value="<?= $arResult['VALUES']['LINK']; ?>"
        />
    </td>
</tr>


