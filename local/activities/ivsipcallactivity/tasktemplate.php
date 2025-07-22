<?php
defined('B_PROLOG_INCLUDED') || die;

/** @var array $arResult */

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Localization\Loc;

/**
 * Функция-помощник для вывода атрибута selected в <option>.
 * @param bool $cond Условие, когда элемент списка должен быть выбран.
 * @return string "selected".
 */
function selected($cond) {
    return $cond ? 'selected' : '';
}

// Для преобразования BB-кодов в HTML используем класс CTextParser.
$parser = new CTextParser();

$required = '<span style="color: red">*</span>';
?>

<!-- Виджет телефонии для совершения звонка. -->
<tr>
    <td valign="top" width="40%" align="right" class="bizproc-field-name">
        <?= Loc::getMessage('CONTACT') ?>
    </td>
    <td valign="top" width="60%" class="bizproc-field-value">
        <?= $arResult['CONTACT_HTML'] ?>
    </td>
</tr>

<!-- Анкета. -->
<? foreach ($arResult['QUESTIONS'] as $question): ?>
    <tr>
        <? if ($question['Type'] == CBPIvSipCallActivity::FIELD_TYPE_NOTE): ?>
            <td colspan="2" style="padding: 10px 0; font-weight: normal;">
                <?= $parser->convertText($question['Note']) ?>
            </td>
        <? else: ?>
            <td valign="top" width="40%" align="right" class="bizproc-field-name">
                <?= $question['Name'] . ($question['Required'] == 'Y' ? $required : '') ?>
            </td>
            <td valign="top" width="60%" class="bizproc-field-value">
                <?
                $value = $question['Value'];

                switch ($question['Type']) {
                    case FieldType::SELECT:

                        ?>
                        <select name="<?= $question['Code'] ?>">
                            <option value="" <?= selected(empty($question['Value'])) ?>></option>
                            <? foreach ($question['Options'] as $option): ?>
                                <option value="<?= $option ?>" <?= selected($value == $option) ?>>
                                    <?= $option ?>
                                </option>
                            <? endforeach; ?>
                        </select>
                        <?
                    break;

                    case FieldType::TEXT:
                        ?><textarea cols="50" rows="3" name="<?= $question['Code'] ?>"><?= $value ?></textarea><?
                    break;

                    default:
                        ?><input name="<?= $question['Code'] ?>" size="50" value="<?= $value ?>"><?
                    break;
                }
                ?>
            </td>
        <? endif; ?>
    </tr>
<? endforeach; ?>
