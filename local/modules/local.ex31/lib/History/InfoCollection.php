<?php

namespace Local\Ex31\History;

use Academy\InvestmentProject\Integration\Intranet\Employee\Employee;
use ArrayIterator;
use IteratorAggregate;

/**
 * @template-implements IteratorAggregate<int, ElementInfo>
 */
final class InfoCollection implements IteratorAggregate
{
    private array $items = [];

    public function __construct(ElementInfo ...$items)
    {
        foreach ($items as $item) {
            $this->insert($item);
        }
    }

    public function insert(ElementInfo $item): void
    {
        $this->items[$item->id] = $item;
    }

    public function get(int $id): ?ElementInfo
    {
        return $this->items[$id] ?? null;
    }

    public function map(callable $mapper): array
    {
        return array_map($mapper, $this->items);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
}