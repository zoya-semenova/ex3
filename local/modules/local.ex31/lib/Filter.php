<?php

namespace Local\Ex31;

use Local\Ex31\Filter\Type\DateRange;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;

final class Filter
{
    public function __construct(
        public readonly ?string $title,
        public readonly ?DateRange $modifyDate,
        public readonly ?string $active,
    ) {
    }

    public function toCriteria(): ConditionTree
    {
        $criteria = new ConditionTree();
        if (isset($this->title)) {
            $title = str_replace('%', '%%', $this->title);
            $criteria->whereLike('TITLE', "%{$title}%");
        }

        if (isset($this->modifyDate)) {
            $this->modifyDate->applyTo($criteria, 'MODIFY_DATE');
        }

        if (isset($this->active)) {//var_dump($this->active);
            $criteria->whereLike('ACTIVE', $this->active);
        }

        return $criteria;
    }
}