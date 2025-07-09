<?php

namespace Local\Ex31\Integration\UI\Filter;

use Bitrix\Main\UI\Filter\FieldAdapter;

final class InfoDataProvider extends DataProvider
{
    public function __construct(
        private readonly ProjectDataProvider $projectDataProvider,
        private readonly InfoSettings $settings
    ) {
    }

    public function getSettings(): InfoSettings
    {
        return $this->settings;
    }

    public function prepareFieldData($fieldID): ?array
    {
        if ($fieldID === 'FIELD_NAME') {
            $projectFields = [];
            foreach ($this->projectDataProvider->prepareFields() as $field) {
                $projectFields[$field->getId()] = $field->getName();
            }
            return [
                'items' => $projectFields,
                'params' => [
                    'multiple' => true
                ]
            ];
        }
        return null;
    }

    public function prepareFields(): array
    {
        return [
            'ID' => $this->createField(
                'ID',
                [
                    'name' => $this->settings->getFieldName('ID'),
                    'type' => FieldAdapter::NUMBER,
                    'default' => false,
                ]
            ),
            'ELEMENT_ID' => $this->createField(
                'ELEMENT_ID',
                [
                    'name' => $this->settings->getFieldName('ELEMENT_ID'),
                    'type' => FieldAdapter::NUMBER,
                    'default' => false,
                ]
            ),
            'TITLE' => $this->createField(
                'TITLE',
                [
                    'name' => $this->settings->getFieldName('TITLE'),
                    'type' => FieldAdapter::TEXTAREA,
                    'default' => true,
                ]
            ),
        ];
    }
}