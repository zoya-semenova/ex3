<?php

namespace Local\Ex31;

use ArrayIterator;
use IteratorAggregate;

/**
 * @template-implements IteratorAggregate<int, Element>
 */
final class Collection implements IteratorAggregate
{
    private array $items = [];

    public function __construct(Element ...$items)
    {
        foreach ($items as $item) {
            $this->insert($item);
        }
    }

    public function insert(Element $project): void
    {
        $this->items[$project->id] = $project;
    }

    public function get(int $id): ?Element
    {
        foreach ($this->items as $item) {
            if ($item->id === $id) {
                return $item;
            }
        }

        return null;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
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
