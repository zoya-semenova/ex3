<?php

namespace Local\Ex31;

use Bitrix\Main\Entity;
use Bitrix\Main\Entity\Query\Join;
use Bitrix\Main\FileTable;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;

class ElementInfoTable extends Entity\DataManager
{
	static function getTableName(): string
	{
		return 'ex31_element_info';
	}

	static function getMap(): array
	{
		return array(
			(new Entity\IntegerField('ID'))
				->configurePrimary(),
            (new Entity\IntegerField('ELEMENT_ID'))
                ->configurePrimary(),
			(new Reference('ELEMENT', ElementTable::class,
				Join::on('this.ELEMENT_ID', 'ref.ID')))
				->configureJoinType('inner'),
            (new StringField('TITLE'))->configureRequired()
                ->addValidator(new LengthValidator(0, 250)),
		);
	}
}
