<?php

\Bitrix\Main\Loader::includeModule('local.ex31');
$dbCon = \Bitrix\Main\Application::getConnection();

$entity = \Local\Ex31\ElementTable::getEntity();
$tableName = \Local\Ex31\ElementTable::getTableName();
if($dbCon->isTableExists($tableName))
{
    $dbCon->queryExecute('DROP TABLE IF EXISTS ' . $tableName);
}
$entity->createDbTable();

\Bitrix\Main\Config\Option::set('local.ex31', 'VERSION', '5');