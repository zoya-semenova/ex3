<?php

namespace Local\Ex31\Crm;

use Bitrix\Crm\PhaseSemantics;
use Bitrix\Crm\Service\Container;
use Bitrix\Crm\Timeline\Entity\TimelineTable;
use Bitrix\Crm\Timeline\TimelineType;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

class Observer
{
    public static function handleOnBeforeCrmDealUpdate(array &$field): bool {
        Loader::requireModule('crm');

        $dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);
        $deal = $dealFactory->getItem($field['ID']);
//echo "<pre>";print_r($deal->getData());echo "</pre>";exit;

if (isset($field['UF_CRM_1752474606351']) && ($field['UF_CRM_1752474606351'] != $deal->getData()['UF_CRM_1752474606351'])) {
    $field['RESULT_MESSAGE'] = 'test deal';

    return false;
}
/*
        if ($deal->getStageId() == $field['STAGE_ID'] || PhaseSemantics::isLost($deal->getStageSemanticId())) {
            return true;
        }

        $newStageSemantics = $dealFactory->getStageSemantics($field['STAGE_ID']);
        if (!PhaseSemantics::isLost($newStageSemantics)) {
            return true;
        }

        $lastComment = self::getLastComment($deal->getId());
        if (empty($lastComment) || $lastComment['AUTHOR_ID'] != $deal->getId()) {
            Loader::requireModule('im');
            $field['RESULT_MESSAGE'] = Loc::getMessage('ERROR_MESSAGE');
            self::sendNotify();
            return false;
        }
*/
        return true;
    }

}