<?php

namespace Local\Ex31\History;

use Bitrix\Main\Type\DateTime;

final class ElementInfo
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $elementId,
        public readonly string $title,
    ) {
    }

    public function withId(int $id): ElementInfo
    {
        return new ElementInfo(
            $id,
            $this->elementId,
            $this->title,
        );
    }
}