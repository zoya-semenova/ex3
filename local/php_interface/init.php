<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandlerCompatible(
    'crm',
    'OnBeforeCrmLeadAdd',
    function (&$arFields) {
        /*
        echo "<pre>";
        print_r($arFields);
        echo "</pre>";
        exit;
*/
    //    if (($arFields['SOURCE_ID'] == 'WEBFORM') && ($arFields['SOURCE_DESCRIPTION'] == 'ORDER_TECH')) {


            $arFields['TITLE'] =
                'Новая заявка с сайта www.komek.ru от посетителя '.$arFields['FM']['PHONE']['n0']['VALUE'];

     //   }


        return $arFields;
    }
);

$eventManager->addEventHandlerCompatible(
    'crm',
    'OnAfterCrmLeadAdd',
    function (&$arFields) {
      //  if (($arFields['SOURCE_ID'] == 'WEBFORM') && ($arFields['SOURCE_DESCRIPTION'] == 'ORDER_TECH')) {
/*
            echo "<pre>";
            print_r($arFields);
            echo "</pre>";
            */
           // exit;

            $obEnum = new \CUserFieldEnum;
            $rsEnum = $obEnum->GetList(array(), array("USER_FIELD_NAME" => 'UF_TAGING2'));

            $enum = array();
            while ($arEnum = $rsEnum->Fetch()) {
                $enum[$arEnum["ID"]] = $arEnum["VALUE"];
            }

            $entryID = \Bitrix\Crm\Timeline\CommentEntry::create(
                array(
                    'TEXT' =>
'[b]Дата и время: [/b]' . $arFields['DATE_CREATE'] . '
[b]От: [/b]' . $arFields['NAME'] . ' ' . $arFields['FM']['PHONE']['n0']['VALUE'] . '
[b]Источник: [/b]' . 'Заявка с сайта' . '
[b]Регион: [/b]' . $arFields['UF_CRM_1538479386'] . '
[b]Тип услуги: [/b]' . $enum[$arFields['UF_TAGING2']] . '
[b]Сообщение: [/b]' . $arFields['COMMENTS'] . '
'
//.$enum[$arFields['UF_CRM_1752222856585']]
,
                    'FILES' => [],
                    //'SETTINGS' => $settings,
                    //'AUTHOR_ID' => $authorID,
                    'BINDINGS' => array(array('ENTITY_TYPE_ID' => CCrmOwnerType::Lead,
                        'ENTITY_ID' => $arFields['ID']))
                )
            );


     //   }

        return $arFields;

    }
);


//Loader::includeModule('exam31.ticket1');