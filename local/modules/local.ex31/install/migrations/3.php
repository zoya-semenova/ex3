<?php

\Bitrix\Main\Loader::includeModule('local.ex31');
$dbCon = \Bitrix\Main\Application::getConnection();

$entity = \Local\Ex31\History\ElementInfoTable::getEntity();
$tableName = \Local\Ex31\History\ElementInfoTable::getTableName();
if(!$dbCon->isTableExists($tableName))
{
    $entity->createDbTable();
}

\Bitrix\Main\Config\Option::set('local.ex31', 'VERSION', '3');