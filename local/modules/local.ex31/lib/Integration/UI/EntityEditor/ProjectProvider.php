<?php

namespace Local\Ex31\Integration\UI\EntityEditor;

use Local\Ex31\Integration\Intranet\Employee\Employee;
use Local\Ex31\Integration\Intranet\Employee\Service as EmployeeService;
use Local\Ex31\Integration\Intranet\Employee\ServiceException;
use Local\Ex31\Integration\UI\FieldNameProvider;
use Local\Ex31\Element;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Localization\Loc;
use Bitrix\UI\EntityEditor\BaseProvider;

/**
 * Строитель параметров формы компонента bitrix:ui.form.
 */
final class ProjectProvider extends BaseProvider
{
    public function __construct(
        private readonly ?Element          $item,
        private readonly FieldNameProvider $fieldNameProvider,
    ) {
    }

    /**
     * Системный метод, нерелевантный для текущего урока.
     *
     * @return string
     */
    public function getGUID(): string
    {
        return 'INVESTMENT_PROJECT';
    }

    /**
     * Системный метод, нерелевантный в рамках текущего урока.
     *
     * @return string
     */
    public function getEntityTypeName(): string
    {
        return 'element';
    }

    public function getEntityId(): ?int
    {
        return $this->item?->id;
    }

    /**
     * Подготавливает инфомрацию о всех полях, доступных в карточке детального просмотра проекта.
     * За структуру отображения полей (в каких разделах карточки какие поля находятся) отвечает {@link ProjectProvider::getEntityConfig()}
     *
     * @return array[]
     */
    public function getEntityFields(): array
    {
        $exists = isset($this->item);
//echo "<pre>";print_r($this->item);echo "</pre>";
        return [
            [
                'name' => 'ID',
                'title' => $this->fieldNameProvider->getProjectFieldName('ID'),
                'editable' => false,
                'type' => 'hidden',
            ],
            [
                'name' => 'TITLE',
                'title' => $this->fieldNameProvider->getProjectFieldName('TITLE'),
                'editable' => true,
                'type' => 'text',
            ],
            [
                'name' => 'TEXT',
                'title' => $this->fieldNameProvider->getProjectFieldName('TEXT'),
                'editable' => true,
                'type' => 'textarea',
            ],
            [
                'name' => 'MODIFY_DATE',
                'title' => $this->fieldNameProvider->getProjectFieldName('MODIFY_DATE'),
                'editable' => false,
                'type' => $exists ? 'datetime' : 'hidden',
            ],
            [
                'name' => 'ACTIVE',
                'title' => $this->fieldNameProvider->getProjectFieldName('ACTIVE'),
                'editable' => true,
                'type' => 'boolean',
                /*
                'data' => array('baseType' => 'char', 'value' => $this->item->active ? 'Y' : 'N'),
                //'showAlways' => true,
                'default_value' => 'Y',
                'value' => ($this->item->active ? 'Y' : 'N'),
                */
                /*
                'data' => array('baseType' => 'int', 'value' => $this->item->active),
                //'showAlways' => true,
                'default_value' => 1,
                'value' => ($this->item->active),
                */
            ],
        ];
    }

    /**
     * Добавляет системные параметры проекта только для существующих проектов.
     *
     * @return array[]
     */
    public function getEntityConfig(): array
    {
        $config = [
            [
                'type' => 'column',
                'name' => 'default_column',
                'elements' => [
                    [
                        'name' => 'main',
                        'title' => Loc::getMessage('INVESTMENT_PROJECT_MAIN_SECTION_TITLE'),
                        'type' => 'section',
                        'elements' => [
                            ['name' => 'ID'],
                            ['name' => 'TITLE'],
                            ['name' => 'TEXT'],
                            ['name' => 'ACTIVE'],
                            ['name' => 'MODIFY_DATE'],
                        ]
                    ],

                ]
            ]
        ];

        return $config;
    }

    /**
     * Возвращает информацию о проекте. Для проекта в режиме создания возвращает предзаполненные поля.
     * Предзаполнить можно только ответственного по проекту, это текущий пользователь.
     *
     * @throws ServiceException
     */
    public function getEntityData(): array
    {

        return array_merge(
            [
                'ID' => $this->item->id,
                'TITLE' => $this->item->title,
                'TEXT' => $this->item->text,
                'ACTIVE' => $this->item->active ? 'Y' : 'N',
                'MODIFY_DATE' => $this->item->modifyDate?->toString(),
            ]
        );
    }

    /**
     * Определяет заголовок страницы, а не значение поля "название" проекта.
     *
     * @return string
     */
    public function getEntityTitle(): string
    {
        return $this->item?->title ?? Loc::getMessage('INVESTMENT_PROJECT_DEFAULT_ENTITY_TITLE');
    }
}