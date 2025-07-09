<?php

namespace Local\Ex31;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\Entity;
use Bitrix\Main\Entity\Query\Join;
use Bitrix\Main\FileTable;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Local\Ex31\History\ElementInfo;

final class ElementTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'ex31_element';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))->configurePrimary()->configureAutocomplete(),
            (new StringField('TITLE'))->configureRequired()
                ->addValidator(new LengthValidator(0, 250)),
            (new DatetimeField('MODIFY_DATE'))->configureRequired()
                ->configureDefaultValue(new DateTime()),
            /*
            (new Entity\BooleanField('ACTIVE'))
                ->configureStorageValues('N', 'Y')
                ->configureDefaultValue('Y'),
            */
            (new BooleanField('ACTIVE'))
                ->configureValues('N', 'Y')
                ->configureDefaultValue('Y'),
            (new DatetimeField('UPDATED_AT')),
            (new TextField('TEXT')),
            (new OneToMany('INFO', ElementInfoTable::class, 'ELEMENT')
                )->configureJoinType('left')
        ];
    }
}