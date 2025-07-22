<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Новый раздел");

/*
CModule::IncludeModule('crm');
use Bitrix\Crm\Service\Container;

$factory = \Bitrix\Crm\Service\Container::getInstance()->getFactory(1036);

$list = $factory->getItemsFilteredByPermissions(
	[
	],
	1,
	\Bitrix\Crm\Service\UserPermissions::OPERATION_READ
			);

foreach($list as $item)
{
	$itemData = $item->getData();


	echo "<pre>";print_r($itemData);exit;


	$itemData = array_diff_key($itemData, $notAccessibleFields);
	$itemsData[$itemData['ID']] = $itemData;

	if ($isExportEventEnabled && $this->isExportMode())
	{
		$trackedObject = $this->factory->getTrackedObject($item);
		Container::getInstance()->getEventHistory()->registerExport($trackedObject);
	}
	$listById[$item->getId()] = $item;
}

echo "<pre>";print_r($list);//exit;
*/
?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>