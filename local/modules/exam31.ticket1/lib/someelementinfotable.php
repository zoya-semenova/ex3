<?php

namespace Exam31\Ticket1;

use Bitrix\Main\Entity;
use Bitrix\Main\Entity\Query\Join;
use Bitrix\Main\FileTable;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;
use Exam31\Ticket1\SomeElementTable;
use Local\Ex31\ElementTable;

final class SomeElementInfoTable extends DataManager
{
	static function getTableName(): string
	{
		return 'exam31_ticket_someelement_info';
	}

	public static function getMap(): array
	{
		return array(
			(new IntegerField('ID'))
				->configurePrimary()
                ->configureAutocomplete(),
            (new IntegerField('ELEMENT_ID'))
                ->configurePrimary(),
            (new DatetimeField('UPDATED_AT')),
			(new Reference('ELEMENT', SomeElementTable::class,
				Join::on('this.ELEMENT_ID', 'ref.ID')))
				->configureJoinType('inner'),
            (new StringField('TITLE'))->configureRequired()
                ->addValidator(new LengthValidator(0, 250)),
		);
	}
}
