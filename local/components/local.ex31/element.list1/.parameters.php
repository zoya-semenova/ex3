<?php B_PROLOG_INCLUDED === true || die();

use Bitrix\Main\Localization\Loc;

$arComponentParameters = [
	'PARAMETERS' => [
		'ELEMENT_COUNT' => [
			'PARENT' => 'BASE',
			'NAME' => Loc::getMessage('NOTIFICATION_LIST_ELEMENT_COUNT'),
			'TYPE' => 'STRING',
		],
		'DETAIL_URL' => [
			'PARENT' => 'BASE',
			'NAME' => Loc::getMessage('NOTIFICATION_LIST_DETAIL_URL'),
			'TYPE' => 'STRING',
		],
		'CACHE_TIME' => ['DEFAULT' => 36000000],
	],
];
