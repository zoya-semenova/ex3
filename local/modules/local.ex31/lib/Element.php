<?php

namespace Local\Ex31;

use Bitrix\Main\Type\DateTime;
use Local\Ex31\History\InfoCollection;

final class Element
{
    /**
     * @param int|null $id Идентификатор
     * @param string $title Название
     * @param DateTime|null $modifyDate Когда создано
     * @param int|null $active Кем создано
     * @param string $text Доход
     */
    public function __construct(
        public readonly ?int $id,
        public string $title,
        public ?DateTime $modifyDate,
        public ?string $active,
        public string $text,
        public ?InfoCollection $info
    ) {

    }

    public function withId(int $id): Element
    {
        return new Element(
            $id,
            $this->title,
            $this->modifyDate,
            $this->active,
            $this->text,
            $this->info
        );
    }
}