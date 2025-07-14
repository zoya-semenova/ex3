<?php

namespace B24\Academy\Crm\Deal;

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

        if (!isset($field['STAGE_ID'])) {
            return true;
        }

        $dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);
        $deal = $dealFactory->getItem($field['ID']);

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

        return true;
    }

    public static function getLastComment(int $dealId): array {
        $result = TimelineTable::getList([
            'filter' => [
                '=BINDINGS.ENTITY_ID' => $dealId,
                '=BINDINGS.ENTITY_TYPE_ID' => \CCrmOwnerType::Deal,
                'TYPE_ID' => TimelineType::COMMENT,
            ],
        ]);
        return $result->fetch() ?: array();
    }

    private static function sendNotify(): void {
        global $USER;

        \CIMNotify::Add([
            'TO_USER_ID' => $USER->GetID(),
            'MESSAGE'    => Loc::getMessage('ERROR_MESSAGE'),
        ]);
    }
}