<?php

namespace B24\Academy\Crm\Kanban;


use Bitrix\Crm\Kanban\Entity\Deal;
use Bitrix\Crm\PhaseSemantics;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;

use Bitrix\Crm\Item;
use Bitrix\Crm\Service\Operation;
use Bitrix\Crm\Service\Container;
use Bitrix\Crm\Service\Context;
use Bitrix\Main\DI;
use Bitrix\Crm\Service\Factory;
use Bitrix\Main\ORM\Objectify\Values;

Loader::requireModule('crm');

class DealEntity extends Deal
{
    public static function registerService(): void {
        $serviceLocator = ServiceLocator::getInstance();
        $serviceLocator->addInstance('crm.service.factory.deal', new DealEntity());


        $entityTypeId = 129;

        $elementId = 37;

        $factory = \Bitrix\Crm\Service\Container::getInstance()->getFactory($entityTypeId);
        echo "<pre>";print_r($factory::class);echo "</pre>";exit;
        $item = $factory->getItem($elementId);
//$item->setTitle('Test');

// Step 1: get opetation
        $operation = $factory->getUpdateOperation($item);
/*
// Step 2: config operation (optional)
$operation
    ->addAction(
        Service\Operation::ACTION_BEFORE_SAVE,
        new class extends Service\Operation\Action {
            public function process( Crm\Item $item ): Main\Result
            {
                $result = new Main\Result();

                $item->setTitle('123');


                return $result;
            }
        }
    );
*/
// Step 3: launch operation
//$operationResult = $operation->launch();


// our new custom container
$container = new class extends Service\Container {
    public function getFactory(int $entityTypeId): ?Factory
    {
        if ($entityTypeId ===129) {
            $type = $this->getTypeByEntityTypeId($entityTypeId);

            $factory = new class($type) extends Factory\Dynamic {
                public function getUpdateOperation(Item $item, Context $context = null): Operation\Update
                {
                    $operation = parent::getUpdateOperation($item, $context);

                    return $operation->addAction(
                        Operation::ACTION_BEFORE_SAVE,
                        new class extends Operation\Action {
                            public function process(Item $item): Result
                            {
                                $result = new Result();
                                $item->setTitle('123');
                                //получаем значения
                                $updatingFields = $item->getData();
//echo "<pre>";print_r($updatingFields);echo "</pre>";exit;
                                if (1) {
                                   // $result->addError(new Error('Запрет редактирования'));
                                }
                                return $result;
                            }
                        }
                    );
                }
            };
            return $factory;
        }
        return parent::getFactory($entityTypeId);
    }
};
// here we change the container
DI\ServiceLocator::getInstance()->addInstance('crm.service.container', $container);

    }

    public function updateItemStage(int $id, string $stageId, array $newStateParams, array $stages): Result
    {
        $result = $this->getItemViaLoadedItems($id);
        if (!$result->isSuccess()) {
            return $result;
        }

        $item = $result->getData()['item'];
        $newStage = $this->factory->getStage($stageId);
        if ($item['STAGE_ID'] == $stageId || !PhaseSemantics::isLost($newStage->getSemantics())) {
            return parent::updateItemStage($id, $stageId, $newStateParams, $stages);
        }

        $lastComment = Observer::getLastComment($id);
        if (empty($lastComment) || $lastComment['AUTHOR_ID'] != $item['ASSIGNED_BY_ID']) {
            return $result->addError(new Error(Loc::getMessage('ERROR_MESSAGE')));
        }

        return parent::updateItemStage($id, $stageId, $newStateParams, $stages);
    }
}