<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\UI\Extension;


$eventManager = EventManager::getInstance();

$eventManager->registerEventHandler(
    'main',
    'OnEpilog',
    'exam31.ticket1',
    Exam31\Ticket1\RuleInjector::class,
    'injectAnchorRules'
);

$eventManager->registerEventHandler(
    'main',
    'OnEpilog',
    'exam31.ticket1',
    'Exam31\Ticket1\LeftMenuExtender',
    'handleOnEpilog'
);

$eventManager->registerEventHandlerCompatible(
    'main',
    'OnUserTypeBuildList',
    'exam31.ticket1',
    \Exam31\Ticket1\ExamFieldType::class,
    'getUserTypeDescription'
);

