<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arActivityDescription = [
    'NAME' => '123',
    'DESCRIPTION' => Loc::getMessage('PAUSE_UNTIL_CHANGE_ACTIVITY_DESCRIPTION'),

    'JSCLASS' => 'BizProcActivity',
    'TYPE' => 'activity',

    'CLASS' => 'ExActivity',
    'CATEGORY' => [
        'ID' => 'other'
    ],
    /*
    'RETURN' => [

        'ObservedFields' => [
            "NAME" => "Список задач",
            "TYPE" => "string",
        ],
    ]
    */
    'ADDITIONAL_RESULT' => array('Fields')
];