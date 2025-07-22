<?php

namespace Local\Ex31;

use Bitrix\Im\Model\RelationTable;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Query;
use Local\Ex31\History\ElementInfo;
use Local\Ex31\History\Entry as HistoryItem;
use Local\Ex31\History\Service as HistoryService;
use Local\Ex31\History\ServiceException as HistoryServiceException;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\ORM\Fields\FieldTypeMask;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UI\PageNavigation;
use Exception;

class Service
{
    public const TRACKED_FIELDS = [

    ];

    /**
     * Не использует транзакции, но должен.
     * Применение и управление транзакциями выходят за пределы темы урока.
     */
    public function __construct(
    ) {
    }

    /**
     * Подсчитывает общее количество Инвест Проектов по заданному фильтру.
     * Используется для корректной работы {@link PageNavigation}.
     *
     * @throws ServiceException
     */
    public function count(Filter $filter): int
    {
        try {
            return ElementTable::query()->where($filter->toCriteria())->queryCountTotal();
        } catch (SystemException $e) {
            throw new ServiceException('Failed to count projects', previous: $e);
        }
    }

    /**
     * Получает фрагмент списка Инвест Проектов.
     *
     * Фрагмент по определению не может содержать полный список Инвестиционных проектов, поэтому параметры
     * $size и $pageNumber - обязательные.
     *
     * @throws ServiceException
     */
    public function getFragment(Filter $filter, array $order, int $size, int $pageNumber): Collection
    {
        if ($size < 0) {
            throw new ServiceException('Page size MUST be a positive integer.');
        }

        $offset = (max($pageNumber, 1) - 1) * $size;

        return $this->findProjects($filter->toCriteria(), $order, $size, $offset);
    }

    /**
     * Общая часть для всех публичных методов, которым необходимо получение Инвест Проектов из БД.
     *
     * @throws ServiceException
     */
    private function findProjects(
        ConditionTree $criteria,
        array $order,
        ?int $limit = null,
        ?int $offset = null
    ): Collection {
        try {
            $result =  (new Query(
                ElementTable::getEntity()
            ))
                ->setSelect([
                    'ID',
                    'TITLE',
                    'MODIFY_DATE',
                    'ACTIVE',
                    'TEXT',
                   // 'INFO_ID' => 'INFO.ID',
                    'cnt'
                   // 'BOOKS.ID',
                   // 'BOOKS.COUNT',
                  //  'BOOKS.ID',
                  //  'BOOKS1'
                ])
                /*
                ->registerRuntimeField('BOOKS1',
                    new ExpressionField('COUNT', '"nnn"'))
                */
                //->setGroup(['INFO.ID'])
                /*
                ->registerRuntimeField(
                    'elements', 'INFO'
                )
                */
                    /*
                ->registerRuntimeField(
                    'USER_TOPIC',
                    new Reference(
                        'USER_TOPIC',
                    )
                )
                    */
                    /*
                ->registerRuntimeField(
                    'RELATION',
                    $this->
                )
                    */
/*
                ->registerRuntimeField(
                    'BOOKS' ,

                     (new Query(
                        ElementInfoTable::getEntity()
                    ))
                        ->setSelect(['ID', 'ELEMENT_ID', 'COUNT'])
                        ->setGroup(['ELEMENT_ID'])
                        ->registerRuntimeField('',
                            new ExpressionField('COUNT', 'COUNT(*)'))
                )
*/
/*
                  ->registerRuntimeField(
                    'BOOKS' ,

                    new ReferenceField('BOOKS2', Entity::getInstanceByQuery( (new Query(
                        ElementInfoTable::getEntity()
                    ))
                        ->setSelect(['ID', 'ELEMENT_ID', 'COUNT'])
                        ->setGroup(['ELEMENT_ID'])
                        				->registerRuntimeField('',
                                            new ExpressionField('COUNT', 'COUNT(*)'))
                    ),
                        ['=this.ID' => 'ref.ELEMENT_ID'],
                        array('join_type' => 'LEFT')
                    ),

                    [
               // 'data_type' => ElementInfoTable::query()
               //     ->setSelect(['ELEMENT_ID', 'BOOK_COUNT' => 'COUNT(*)'])
                //    ->setGroup(['ELEMENT_ID']),
                  //      'data_type' =>      ,
               // 'reference' =>
            ]
                )
*/

                ->registerRuntimeField("cnt", array(
                        "data_type" => "integer",
                        "expression" => array("count(%s)", "INFO.ID")
                    )
                )

                //->where($criteria)
              //  ->setOrder($order);
                //->setLimit($limit)
               // ->setOffset($offset);
                //->exec();
            ;

         // echo "<pre>";
          //  print_r( $result->getQuery());

          //  $r = $result->exec();
           // print_r($r->get)
          //  print_r($result->fetchAll());exit;
            $projects = new Collection();
            foreach ($result->fetchAll() as $entityArr) {
                // echo "<pre>"; print_r($entityObject->getInfo()->getAll());exit;
               // foreach ($entityObject->getInfo() as $info) {
                   // echo "<pre>"; print_r($info->getTitle());
              //  }
             //   echo "<pre>"; print_r($entityArr);
                //$entityObject = new ElementTable();
                $entityObject = ElementTable::createObject();
                $entityObject->set('ID', $entityArr['ID']);
                $entityObject->set('TITLE', $entityArr['TITLE']);
                $entityObject->set('MODIFY_DATE', $entityArr['MODIFY_DATE']);
                $entityObject->set('ACTIVE', $entityArr['ACTIVE']);
                $entityObject->set('TEXT', $entityArr['TEXT']);
               // $entityObject->fill($entityArr);
               // print_r($entityObject);
               // exit;
                $projects->insert(
                    new Element(
                        $entityObject->getId(),
                        $entityObject->getTitle(),
                        $entityObject->getModifyDate(),
                        $entityObject->getActive(),
                        $entityObject->getText(),
$entityArr['cnt']
                    )
                );
            }

            return $projects;
        } catch (SystemException $e) {
            print_r($e->getMessage());
            throw new ServiceException('Failed to find project', previous: $e);
        }
    }

    /**
     * Создает Инвест Проект.
     *
     * Всегда задает значения для системных полей (кем создано, когда создано, кем изменено, когда изменено).
     *
     * Все значения изменяемых пользователем полей записываются в Историю изменений проекта.
     *
     * @throws ServiceException
     */
    public function create(Element $project): Element
    {
        $currentTime = new DateTime();

        $project->modifyDate = $currentTime;

        try {
            $entityObject = ElementTable::createObject()
                ->setTitle($project->title)
                ->setModifyDate($project->modifyDate)
                ->setActive($project->active)
                ->setText($project->text);

            $addResult = $entityObject->save();
        } catch (Exception $e) {
            throw new ServiceException('Failed to add project', previous: $e);
        }

        if (!$addResult->isSuccess()) {
            throw ServiceException::createFromCollection($addResult->getErrorCollection());
        }

        $project = $project->withId($addResult->getId());

        return $project;
    }

    /**
     * Обновляет Инвест Проект.
     *
     * Всегда задает значения для полей "кем изменено" и "когда изменено".
     *
     * Записывает в Историю изменений те поля, которые были изменены в сравнении с предыдущим состоянием в БД.
     * Значения полей сравниваются нестрого.
     *
     * Изменения значений системных полей в историю не попадают.
     *
     * @throws NotFoundException
     * @throws ServiceException
     */
    public function update(Element $project): void
    {
        $project->modifyDate = new DateTime();

        try {
            $actualProject = ElementTable::getById($project->id)->fetchObject();
        } catch (SystemException $e) {
            throw new NotFoundException('Project not found', previous: $e);
        }

        try {
            $entityObject = $actualProject
                ->setTitle($project->title)
                ->setModifyDate($project->modifyDate)
                ->setActive($project->active)
                ->setText($project->text);

            foreach ($entityObject->collectValues(fieldsMask: FieldTypeMask::FLAT) as $field => $currentValue) {
                if (!in_array($field, Service::TRACKED_FIELDS, true)) {
                    continue;
                }

                if (!$entityObject->isChanged($field)) {
                    continue;
                }
            }

            $updateResult = $entityObject->save();
        } catch (Exception $e) {
            throw new ServiceException('Failed to update project', previous: $e);
        }

        if (!$updateResult->isSuccess()) {
            throw ServiceException::createFromCollection($updateResult->getErrorCollection());
        }
    }

    /**
     * Получает конкретный Инвест Проект по идентификатору из БД.
     *
     * @throws NotFoundException
     * @throws ServiceException
     */
    public function getById(int $projectId): Element
    {
        $criteria = new ConditionTree();
        try {
            $criteria->where('ID', '=', $projectId);
        } catch (ArgumentException) {
            // Noop, never thrown.
        }

        $projects = $this->findProjects($criteria, ['ID' => 'DESC']);

        if ($projects->isEmpty()) {
            throw new NotFoundException('Project not found.');
        }

        return $projects->get($projectId);
    }


    /**
     * @throws ServiceException
     */
    public function getByIds(int ...$projectIds): Collection
    {
        if (empty($projectIds)) {
            return new Collection();
        }

        $criteria = new ConditionTree();
        $criteria->whereIn('ID', $projectIds);

        return $this->findProjects($criteria, ['ID' => 'DESC']);
    }

    /**
     * @throws ServiceException
     */
    public function delete(Element $project): void
    {
        try {
            $deleteResult = ElementTable::getById($project->id)->fetchObject()->delete();

        } catch (Exception $e) {
            throw new ServiceException('Failed to delete project', previous: $e);
        }

        if (!$deleteResult->isSuccess()) {
            throw ServiceException::createFromCollection($deleteResult->getErrorCollection());
        }
    }
}