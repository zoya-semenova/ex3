<?php


try {
    \Bitrix\Main\UrlRewriter::add('s1', [
        'ID' => 'local.ex31:element',
        'CONDITION' => '#^/invest/#',
        'PATH' => '/invest/index.php'
    ]);
} catch (ArgumentNullException) {
    // Noop, never happens because $siteId is a string literal.
}

\Bitrix\Main\Config\Option::set('local.ex31', 'VERSION', '4');