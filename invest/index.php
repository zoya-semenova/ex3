<?php


require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';

/**
 * @var CMain $APPLICATION
 */

$APPLICATION->IncludeComponent(
    'local.ex31:element',
    '.default',
    [
        'SEF_FOLDER' => '/invest/',
        'URL_TEMPLATES' => [
            'list' => 'list/',
            'detail' => 'detail/#INVESTMENT_PROJECT_ID#/',
            'history' => 'info/#INVESTMENT_PROJECT_ID#/',
        ],
        'DEFAULT_PAGE' => 'list'
    ]
);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';