<?php

namespace Local\Ex31\Integration\UI\Filter;

use Bitrix\Main\UI\Filter\FieldAdapter;

final class ProjectDataProvider extends DataProvider
{
    public function __construct(private readonly ProjectSettings $settings)
    {
    }

    public function getSettings(): ProjectSettings
    {
        return $this->settings;
    }

    public function prepareFieldData($fieldID): ?array
    {
        return match ($fieldID) {
            default => null
        };
    }

    public function prepareFields(): array
    {
        return [
            'ID' => $this->createField(
                'ID',
                [
                    'name' => $this->settings->getFieldName('ID'),
                    'type' => FieldAdapter::NUMBER,
                    'default' => true,
                ]
            ),
            'TITLE' => $this->createField(
                'TITLE',
                [
                    'name' => $this->settings->getFieldName('TITLE'),
                    'type' => FieldAdapter::STRING,
                    'default' => true,

                ]
            ),
            'MODIFY_DATE' => $this->createField(
                'MODIFY_DATE',
                [
                    'name' => $this->settings->getFieldName('MODIFY_DATE'),
                    'type' => FieldAdapter::DATE,
                    'default' => true,
                ]
            ),
            'ACTIVE' => $this->createField(
                'ACTIVE',
                [
                    'name' => $this->settings->getFieldName('ACTIVE'),
                    'type' => FieldAdapter::CHECKBOX,
                    'default' => true,
                    //'valueType' => 'numeric'
                ]
            ),
            'TEXT' => $this->createField(
                'TEXT',
                [
                    'name' => $this->settings->getFieldName('TEXT'),
                    'type' => FieldAdapter::TEXTAREA,
                    'default' => true,
                ]
            ),

        ];
    }
}