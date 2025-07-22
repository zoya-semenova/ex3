<?php

use B24\Academy\Crm\Deal\Observer;
use B24\Academy\Crm\Kanban\DealEntity;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;


$eventManager = EventManager::getInstance();

$eventManager->registerEventHandler(
    'crm',
    'OnBeforeCrmDealUpdate',
    'local.ex31',
    \Local\Ex31\Crm\Observer::class,
    'handleOnBeforeCrmDealUpdate',
);

$eventManager->registerEventHandlerCompatible(
    'main',
    'OnUserTypeBuildList',
    'local.ex31',
    \Local\Ex31\UserField\Ex31Field::class,
    'getUserTypeDescription'
);

$eventManager->registerEventHandler(
    'main',
    'OnEpilog',
    'local.ex31',
    \Local\Ex31\Integration\UI\SidePanel\RuleInjector::class,
    'injectAnchorRules'
);