<?php
define('PUBLIC_AJAX_MODE', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('NOT_CHECK_PERMISSIONS', true);
define('DisableEventsCheck', true);
define('NO_AGENT_CHECK', true);

/* PROVIDER -> CONTROLLER -> PORTAL */
$_SERVER['DOCUMENT_ROOT'] = '/home/bitrix/www';
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');


if (!\Bitrix\Main\Loader::includeModule('exam31.ticket2'))
{
return false;
}
/*
$dbConnection = \Bitrix\Main\Application::getConnection();
for ($i=0;$i<100;$i++) {
    $entity = \Exam31\Ticket2\SomeElement2Table::createObject();
    //$entity->setId($i);
    $entity->setTitle('title'.$i);
    $entity->setText('text'.$i);
    $entity->setActive(1);
    $entity->save();
}
*/
/*
$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->registerEventHandlerCompatible(
    'main',
    'OnEpilog',
   'exam31.ticket1',
    'Exam31\\Ticket2\\examevents',
    'injectAnchorRules'
);
*/
/*
$dbConnection = \Bitrix\Main\Application::getConnection();
$entity = \Exam31\Ticket2\SomeElementInfo2Table::getEntity();
$tableName = \Exam31\Ticket2\SomeElementInfo2Table::getTableName();
if (!$dbConnection->isTableExists($tableName))
{
    $entity->createDbTable();
}

for ($i=0;$i<10;$i++) {
    $ob = \Exam31\Ticket2\SomeElementInfo2Table::createObject();
    $ob->set('TITLE', 'title '.$i);
    $ob->set('ELEMENT_ID', rand(95,100));
    $ob->save();
}
*/
$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->registerEventHandlerCompatible(
    'main',
    'OnEpilog',
    'exam31.ticket2',
    'Exam31\\Ticket2\\examevents',
    'injectAnchorRules'
);

