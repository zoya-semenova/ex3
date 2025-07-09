<?php

namespace Local\Ex31\Integration\UI\Filter;

use Local\Ex31\Filter\Type\DateRange;
use Bitrix\Main\Filter\DataProvider as MainDataProvider;
use Bitrix\Main\Filter\Field;
use Bitrix\Main\UI\Filter\FieldAdapter;

abstract class DataProvider extends MainDataProvider
{
    public function prepareFilterValue(array $rawFilterValue): array
    {
        foreach ($this->prepareFields() as $field) {
            $rawFilterValue[$field->getId()] = match ($field->getType()) {
                FieldAdapter::DATE => DateRange::createFromArray($rawFilterValue, $field->getId()),
                //FieldAdapter::CHECKBOX => $rawFilterValue[$field->getId()] == 'Y',
                default => $rawFilterValue[$field->getId()]
            };
        }

        return $rawFilterValue;
    }

    public function getFieldSortingName(Field $field): ?string
    {
        return $field->getType() !== FieldAdapter::ENTITY_SELECTOR ? $field->getId() : null;
    }
}