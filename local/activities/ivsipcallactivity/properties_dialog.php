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

<!-- Тип контакта: произвольный номер телефона, контакт CRM. -->
<tr>
    <td align="right" width="40%"><span class="adm-required-field"><?= Loc::getMessage('CONTACT_TYPE') ?>:</span></td>
    <td width="60%">
        <select id="ContactTypeSwitcher" name="ContactType">
            <option value="phone" <? selected($arCurrentValues['ContactType'] == 'phone') ?>>
                <?= Loc::getMessage('CONTACT_TYPE_PHONE') ?>
            </option>
            <option value="crm_contact" <? selected($arCurrentValues['ContactType'] == 'crm_contact') ?>>
                <?= Loc::getMessage('CONTACT_TYPE_CRM_CONTACT') ?>
            </option>
        </select>
    </td>
</tr>

<!-- Поле произвольного номера телефона (отображается, когда выбран соотв. тип контакта). -->
<tr class="switchable on-phone">
    <td align="right" width="40%"><span class="adm-required-field"><?= Loc::getMessage('PHONE_NUMBER') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("string", 'Phone', $arCurrentValues['Phone'], Array('size'=> 50)) ?>
    </td>
</tr>

<!-- Поле указания CRM контакта (отображается, когда выбран соотв. тип контакта). -->
<tr class="switchable on-crm_contact">
    <td align="right" width="40%"><span class="adm-required-field"><?= Loc::getMessage('CRM_CONTACT_ID') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("string", 'CrmContactId', $arCurrentValues['CrmContactId'], Array('size'=> 10)) ?>
    </td>
</tr>

<!-- Ответственный за задание. -->
<tr>
    <td align="right" width="40%"><span class="adm-required-field"><?= Loc::getMessage('RESPONSIBLE') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("user", 'Responsible', $arCurrentValues['Responsible']) ?>
    </td>
</tr>

<!-- Название задания. -->
<tr>
    <td align="right" width="40%"><span class="adm-required-field"><?= Loc::getMessage('ASSIGNMENT_NAME') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("string", 'AssignmentName', $arCurrentValues['AssignmentName'], array('size' => 50)) ?>
    </td>
</tr>

<!-- Постановка задачи. -->
<tr>
    <td align="right" width="40%"><span><?= Loc::getMessage('ASSIGNMENT_DESCRIPTION') ?>:</span></td>
    <td width="60%">
        <?= CBPDocument::ShowParameterField("text", 'AssignmentDescription', $arCurrentValues['AssignmentDescription']) ?>
    </td>
</tr>

<!-- Анкета. -->
<tr>
    <td colspan="2">
        <p><span class="adm-required-field"><?= Loc::getMessage('QUESTIONNAIRE') ?>:</span></p>
        <table class="internal" width="100%">
            <thead>
                <tr class="heading">
                    <td><?= Loc::getMessage('Q_CODE') ?></td>
                    <td><?= Loc::getMessage('Q_NAME') ?></td>
                    <td><?= Loc::getMessage('Q_TYPE') ?></td>
                    <td>&nbsp;</td>
                </tr>
            </thead>
            <tbody id="questions">

                <!-- Шаблон формы вопроса. Копируется и заполняется с помощью JS. -->
                <tr id="qTemplate" class="q-row" data-tmpid="" style="display: none;">
                    <td valign="top" class="q-field-generic"><input class="q-code"></td>
                    <td valign="top" class="q-field-generic">
                        <input class="q-name">
                        <p>
                            <label>
                                <input type="checkbox" class="q-required" value="Y">
                                <?= Loc::getMessage('Q_REQUIRED') ?>
                            </label>
                        </p>
                    </td>
                    <td valign="top" class="q-field-note" colspan="2" style="display: none;">
                        <textarea class="q-note"></textarea>
                    </td>
                    <td valign="top">
                        <select class="q-type">
                            <option value="<?= FieldType::STRING ?>"><?= Loc::getMessage('Q_STRING') ?></option>
                            <option value="<?= FieldType::DOUBLE ?>"><?= Loc::getMessage('Q_NUMBER') ?></option>
                            <option value="<?= FieldType::TEXT ?>"><?= Loc::getMessage('Q_TEXTAREA') ?></option>
                            <option value="<?= FieldType::SELECT ?>"><?= Loc::getMessage('Q_LIST') ?></option>
                            <option value="<?= CBPIvSipCallActivity::FIELD_TYPE_NOTE ?>"><?= Loc::getMessage('Q_NOTE') ?></option>
                        </select>

                        <div class="q-select-options" style="display: none;">
                            <p><?= Loc::getMessage('Q_LIST_OPTIONS') ?></p>
                            <input type="button" class="q-select-option-add" value="+">
                        </div>

                        <p class="q-default-group">
                            <label class="q-default-label"><?= Loc::getMessage('Q_DEFAULT') ?></label><br>
                            <input class="q-default">
                        </p>
                    </td>
                    <td valign="top">
                        <a href="javascript:void(0);" class="q-up"><?= Loc::getMessage('Q_UP') ?></a>
                        |
                        <a href="javascript:void(0);" class="q-down"><?= Loc::getMessage('Q_DOWN') ?></a>
                        <p><a href="javascript:void(0);" class="q-delete"><?= Loc::getMessage('Q_DELETE') ?></a></p>
                    </td>
                </tr>

            </tbody>
        </table>

        <!-- Кнопка создания нового вопроса в анкете. -->
        <p><input type="button" id="qAdd" value="<?= Loc::getMessage('Q_ADD') ?>"></p>
    </td>
</tr>

<style>
    .q-row .q-field-note textarea.q-note {
        width: 100%;
        height: 100%;
        min-width: 300px;
        min-height: 100px;
    }
</style>

<script type="text/javascript">
    BX.ready(function () {

        // Переключатель типа контакта.

        var ContactTypeSwitcher = BX('ContactTypeSwitcher');

        /**
         * Управляет видимостью полей, зависящих от типа контакта.
         * Скрывает все поля с CSS-классом switchable, кроме тех, что имеют CSS-класс on-<тип контакта>.
         * <тип контакта> — значение из селектора типа контакта (id="ContactTypeSwitcher").
         */
        var switchContactType = function () {
            var ContactTypeSwitcher = BX('ContactTypeSwitcher');
            var contactTypeValue = ContactTypeSwitcher.value;

            if (contactTypeValue) {
                var switchable = BX.findChildren(document, {'class': 'switchable'}, true);
                switchable.forEach(function (elem) {
                    BX.adjust(elem, {'style': {'display': 'none'}});
                });

                var contactTypeControls = BX.findChildren(document, {'class': 'switchable on-' + contactTypeValue}, true);
                contactTypeControls.forEach(function (elem) {
                    BX.adjust(elem, {'style': {'display': 'table-row'}})
                });
            }
        };

        if (ContactTypeSwitcher) {
            BX.bind(ContactTypeSwitcher, 'change', switchContactType);
        }

        switchContactType();


        // Редактор анкеты.

        // Вопросы анкеты из настроек шага.
        var Questions = <?= Json::encode(array_values($questions)) ?>;

        var QCounter = 1;
        var QAdd = BX('qAdd');

        /**
         * Генерирует очередной временный идентификатор вопроса анкеты.
         * На каждый вызов возвращает Q1, Q2, Q3...
         * @returns {string} Идентификатор вопроса.
         */
        var getQuestionTmpId = function () {
            return 'Q' + QCounter++;
        };

        /**
         * Управляет отображением полей формы настройки вопроса в зависимости от типа вопроса (строка, список...).
         */
        var switchFieldType = function () {

            var td = BX.findParent(this, {'tag': 'td'});
            var tr = BX.findParent(this, {'tag': 'tr'});
            var optionsEditor = BX.findChildByClassName(td, 'q-select-options', true);
            var defaultEditor = BX.findChildByClassName(td, 'q-default-group', true);
            var fieldsGenericEditor = BX.findChildrenByClassName(tr, 'q-field-generic', true);
            var fieldNoteEditor = BX.findChildByClassName(tr, 'q-field-note', true);

            if (!optionsEditor || !defaultEditor || !fieldsGenericEditor || !fieldNoteEditor) {
                return;
            }

            var displayOptionsEditor = 'none';
            if (this.value == '<?= FieldType::SELECT ?>') {
                displayOptionsEditor = 'block';
            }

            var displayFieldGeneric = 'table-cell';
            var displayFieldNote = 'none';
            var displayDefault = 'block';
            if (this.value == '<?= CBPIvSipCallActivity::FIELD_TYPE_NOTE ?>') {
                displayFieldGeneric = 'none';
                displayFieldNote = 'table-cell';
                displayDefault = 'none';
            }

            BX.adjust(optionsEditor, {'style': {'display': displayOptionsEditor}});

            for (var i in fieldsGenericEditor) {
                BX.adjust(fieldsGenericEditor[i], {'style': {'display': displayFieldGeneric}});
            }

            BX.adjust(fieldNoteEditor, {'style': {'display': displayFieldNote}});
            BX.adjust(defaultEditor, {'style': {'display': displayDefault}});
        };

        /**
         * Добавляет в форму настройки вопроса новое поле ввода варианта значения списка (для типа вопроса "список").
         */
        var addSelectOption = function () {
            var row = BX.findParent(this, {'class': 'q-row'});

            if (!row) {
                return;
            }

            var tmpId = BX.data(row, 'tmpid');

            var input = BX.create('input', {'attrs': {'name': 'Q[' + tmpId + '][Options][]'}});
            var br = BX.create('br');

            this.parentNode.insertBefore(input, this);
            this.parentNode.insertBefore(br, this);
        };

        /**
         * Перемещает вопрос анкеты вверх.
         */
        var moveQuestionUp = function () {
            var row = BX.findParent(this, {'class': 'q-row'});

            if (!row) {
                return;
            }

            var prev = BX.findPreviousSibling(row);
            if (!prev || prev.id == 'qTemplate') {
                return;
            }

            BX.remove(row);
            prev.parentNode.insertBefore(row, prev);
        };

        /**
         * Перемещает вопрос анкеты вниз.
         */
        var moveQuestionDown = function () {
            var row = BX.findParent(this, {'class': 'q-row'});

            if (!row) {
                return;
            }

            var next = BX.findNextSibling(row);
            if (!next) {
                return;
            }

            BX.remove(row);
            BX.insertAfter(row, next);
        };

        /**
         * Удаляет вопрос анкеты.
         */
        var deleteQuestion = function () {
            var row = BX.findParent(this, {'class': 'q-row'});

            if (!row) {
                return;
            }

            BX.remove(row);
        };

        /**
         * Добавляет вопрос в конец анкеты.
         * @param {object} values Настройки вопроса (не обязательный).
         */
        var addQuestion = function (values) {
            var QTable = BX('questions');
            var QTemplate = BX('qTemplate');

            if (!QTable || !QTemplate) {
                return;
            }

            var tmpId = getQuestionTmpId();

            // Копируем шаблон вопроса.
            var Question = BX.clone(QTemplate);
            BX.adjust(
                Question,
                {
                    'attrs': {
                        'style': '',
                        'id': '',
                        'data-tmpid': tmpId
                    },
                }
            );

            // Задаем name, id всем полям, привязываем обработчики событий.
            var QNote = BX.findChildByClassName(Question, 'q-note', true);
            BX.adjust(QNote, {'attrs': {'name': 'Q[' + tmpId + '][Note]'}});

            var QCode = BX.findChildByClassName(Question, 'q-code', true);
            BX.adjust(QCode, {'attrs': {'name': 'Q[' + tmpId + '][Code]'}});

            var QName = BX.findChildByClassName(Question, 'q-name', true);
            BX.adjust(QName, {'attrs': {'name': 'Q[' + tmpId + '][Name]'}});

            var QRequired = BX.findChildByClassName(Question, 'q-required', true);
            BX.adjust(QRequired, {'attrs': {'name': 'Q[' + tmpId + '][Required]'}});

            var QType = BX.findChildByClassName(Question, 'q-type', true);
            BX.adjust(QType, {'attrs': {'name': 'Q[' + tmpId + '][Type]'}});
            BX.bind(QType, 'change', switchFieldType);

            var QDefault = BX.findChildByClassName(Question, 'q-default', true);
            var QDefaultLabel = BX.findChildByClassName(Question, 'q-default-label', true);
            var qName = 'Q[' + tmpId + '][Default]';
            var qId = 'Q_' + tmpId + '_Default';
            BX.adjust(QDefault, {'attrs': {'name': qName, 'id': qId}});
            BX.adjust(QDefaultLabel, {'attrs': {'for': qId}});

            var QUp = BX.findChildByClassName(Question, 'q-up', true);
            BX.bind(QUp, 'click', moveQuestionUp);

            var QDown = BX.findChildByClassName(Question, 'q-down', true);
            BX.bind(QDown, 'click', moveQuestionDown);

            var QDelete = BX.findChildByClassName(Question, 'q-delete', true);
            BX.bind(QDelete, 'click', deleteQuestion);

            var AddSelectOption = BX.findChildByClassName(Question, 'q-select-option-add', true);
            BX.bind(AddSelectOption, 'click', addSelectOption);

            // Если переданы настройки вопроса, заполняем поля данными.
            if (typeof values === 'object') {
                if (values.hasOwnProperty('Note')) {
                    QNote.value = values.Note;
                }

                if (values.hasOwnProperty('Code')) {
                    QCode.value = values.Code;
                }

                if (values.hasOwnProperty('Name')) {
                    QName.value = values.Name;
                }

                if (values.hasOwnProperty('Required') && values.Required == 'Y') {
                    QRequired.checked = true;
                }

                if (values.hasOwnProperty('Type')) {
                    QType.value = values.Type;

                    if (values.Type == '<?= FieldType::SELECT ?>') {
                        var OptionsEditor = BX.findChildByClassName(Question, 'q-select-options', true);
                        BX.adjust(OptionsEditor, {'style': {'display': 'block'}});
                    }

                    if (values.Type == '<?= CBPIvSipCallActivity::FIELD_TYPE_NOTE ?>') {
                        var defaultEditor = BX.findChildByClassName(Question, 'q-default-group', true);
                        var fieldsGenericEditor = BX.findChildrenByClassName(Question, 'q-field-generic', true);
                        var fieldNoteEditor = BX.findChildByClassName(Question, 'q-field-note', true);

                        for (var i in fieldsGenericEditor) {
                            BX.adjust(fieldsGenericEditor[i], {'style': {'display': 'none'}});
                        }
                        BX.adjust(fieldNoteEditor, {'style': {'display': 'table-cell'}});
                        BX.adjust(defaultEditor, {'style': {'display': 'none'}});
                    }
                }

                if (values.hasOwnProperty('Options') && values.Options instanceof Array) {
                    for (var i in values.Options) {
                        var input = BX.create('input', {'attrs': {'name': 'Q[' + tmpId + '][Options][]'}});
                        input.value = values.Options[i];

                        var br = BX.create('br');

                        AddSelectOption.parentNode.insertBefore(input, AddSelectOption);
                        AddSelectOption.parentNode.insertBefore(br, AddSelectOption);
                    }
                }

                if (values.hasOwnProperty('Default')) {
                    QDefault.value = values.Default;
                }
            }

            BX.append(Question, QTable);
        };

        if (QAdd) {
            BX.bind(QAdd, 'click', addQuestion);
        }

        // Заполняем анкету вопросами после загрузки формы (см. откуда берется Question).
        for (var i in Questions) {
            addQuestion(Questions[i]);
        }
    });
</script>