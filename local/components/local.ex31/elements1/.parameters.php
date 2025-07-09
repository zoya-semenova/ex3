<?php B_PROLOG_INCLUDED === true || die();

use Bitrix\Main\Localization\Loc;

$arComponentParameters = [
	'PARAMETERS' => [
		'VARIABLE_ALIASES' => [
			'ID' => ['NAME' => GetMessage('NOTIFICATIONS_VARIABLE_ALIASES_ID_NAME')],
		],
		'SEF_MODE' => [
			'list' => [
				'NAME' => Loc::getMessage('NOTIFICATIONS_SEF_MODE_LIST_PARAMETER_NAME'),
				'DEFAULT' => '',
				'VARIABLES' => [],
			],
			'detail' => [
				'NAME' => Loc::getMessage('NOTIFICATIONS_SEF_MODE_DETAIL_PARAMETER_NAME'),
				'DEFAULT' => '#ID#/',
				'VARIABLES' => ['ID'],
			],
		],
		'CACHE_TIME' => ['DEFAULT' => 36000000],
	],
];
