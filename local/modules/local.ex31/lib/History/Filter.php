<?php

namespace Local\Ex31\History;

use Local\Ex31\Filter\Type\DateRange;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;

final class Filter
{
    public function __construct(
        public readonly ?int $elementId,
        public readonly ?string $title,
        public readonly ?array $ids
    ) {
    }

    public function toCriteria(): ConditionTree
    {
        $criteria = new ConditionTree();

        try {
            if (isset($this->elementId)) {
                $criteria->where('ELEMENT_ID', '=', $this->elementId);
            }

            if (isset($this->title)) {
                $title = str_replace('%', '%%', $this->title);
                $criteria->whereLike('TITLE', $title);
            }

            if (isset($this->ids)) {
                $criteria->whereIn('ID', $this->ids);
            }
        } catch (ArgumentException) {
            // noop, never happens.
        }

        return $criteria;
    }
}