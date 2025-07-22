<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arActivityDescription = array(
    'NAME' => Loc::getMessage('SIPCALL_NAME'),
    'DESCRIPTION' => Loc::getMessage('SIPCALL_DESCRIPTION'),
    'TYPE' => 'activity',
    'CLASS' => 'IvSipCallActivity',
    'JSCLASS' => 'BizProcActivity',
    'CATEGORY' => array(
        'ID' => 'other',
    ),
    'ADDITIONAL_RESULT' => array('QuestionnaireResults')
);