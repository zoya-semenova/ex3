<?
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

/** @var array $arCurrentValues */
/** @var string $formName */

Loc::loadMessages(__FILE__);

/**
 * Функция-помощник для вывода атрибута selected в <option>.
 * @param bool $condition Условие, когда элемент списка должен быть выбран.
 */
function selected($condition) {
    if ($condition) {
        echo 'selected';
    }
}

/* Если пользователь нажал "сохранить" и данные не прошли валидацию, будем заполнять форму настройки
 * последними данными, которые отправил пользователь ($arCurrentValues['Q']).
 *
 * Если окно настройки открыли первый раз, заполняем ее данными из сохраненных
 * ранее настроек шага ($arCurrentValues['QuestionnaireResults']).
 */
if (empty($arCurrentValues['Q'])) {
    $questions = $arCurrentValues['QuestionnaireResults'];
} else {
    $questions = $arCurrentValues['Q'];
}
?>

<tr>
    <td align="right" width="40%"><span>ElId:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("string", 'ElId', $arCurrentValues['ElId']) ?>
    </td>
</tr>
<tr>
    <td align="right" width="40%"><span>111:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("string", '111', $arCurrentValues['111']) ?>
    </td>
</tr>

<tbody id="crm_entity_fields"><?/*
		$html = '';

        $options = '';
		foreach ($arCurrentValues['Fields'] as $fieldId => $field)
		{
			$selected = '';
			//if (is_array($currentValues['EntityFields']) && array_key_exists($fieldId, $currentValues['EntityFields']))
			//	$selected = 'selected';
			$options .= '<option '.$selected.' value="'.$fieldId.'">'.$field['Name'].'</option>';
		}


		$html .= '
			<tr>
				<td align="right" width="40%" class="adm-detail-content-cell-l">
					<span style="font-weight: bold">fields</span>
				</td>
				<td width="60%">
					<select name="Fields[]" multiple>'.$options.'</select>
				</td>
			</tr>
		';

		echo $html;
*/
?></tbody>