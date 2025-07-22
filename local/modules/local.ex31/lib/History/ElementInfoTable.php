<?php

namespace Local\Ex31\History;

use Bitrix\Main\Entity;
use Bitrix\Main\Entity\Query\Join;
use Bitrix\Main\FileTable;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;
use Local\Ex31\ElementTable;

final class ElementInfoTable extends DataManager
{
	static function getTableName(): string
	{
		return 'ex31_element_info';
	}

	public static function getMap(): array
	{
		return array(
			(new IntegerField('ID'))
				->configurePrimary(),
            (new IntegerField('ELEMENT_ID'))
                ->configurePrimary(),
            (new DatetimeField('UPDATED_AT')),
			(new Reference('ELEMENT', ElementTable::class,
				Join::on('this.ELEMENT_ID', 'ref.ID')))
				->configureJoinType('inner'),
            (new StringField('TITLE'))->configureRequired()
                ->addValidator(new LengthValidator(0, 250)),
		);
	}
}
