<?php B_PROLOG_INCLUDED === true || die();

use Bitrix\Main\Localization\Loc;

$arComponentParameters = [
	'PARAMETERS' => [
		'NOTIFICATION_ID' => [
			'PARENT' => 'BASE',
			'NAME' => Loc::getMessage('NOTIFICATION_DETAIL_NOTIFICATION_ID'),
			'TYPE' => 'STRING',
		],
		'LIST_URL' => [
			'PARENT' => 'BASE',
			'NAME' => Loc::getMessage('NOTIFICATION_DETAIL_LIST_URL'),
			'TYPE' => 'STRING',
		],
		'CACHE_TIME' => ['DEFAULT' => 36000000],
	],
];
