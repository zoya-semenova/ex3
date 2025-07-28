<?php

use Exam31\Ticket1\SomeElementTable;


use \Bitrix\Main\Loader;

define('NO_KEEP_STATISTIC', true); //запрет сбора статистики
define('NOT_CHECK_PERMISSIONS', true); //отключение проверки прав на доступ к файлам и каталогам
define('BX_BUFFER_USED', true); // сбросит уровень буферизации CMain::EndBufferContent
define('LID', "s1");

if (empty($_SERVER["DOCUMENT_ROOT"])) {//DOCUMENT_ROOT может быть не определён, поэтому определим его сами
    $_SERVER["DOCUMENT_ROOT"] = '/home/bitrix/www';
}
require("/home/bitrix/www/bitrix/modules/main/include/prolog_before.php"); //подключаем ядро

set_time_limit(0);


\Bitrix\Main\Loader::includeModule('exam31.ticket1');

for ($i = 0; $i < 100; $i++) {
    $entityObject = SomeElementTable::createObject()
        ->setTitle('Название '.$i)
        ->setDateModify((new \Bitrix\Main\Type\DateTime()))
        ->setActive(1)
        ->setText('Описание '.$i);

    $addResult = $entityObject->save();
}
