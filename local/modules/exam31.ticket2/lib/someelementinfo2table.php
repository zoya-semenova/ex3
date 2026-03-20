<?php
namespace Exam31\Ticket2;

use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Localization\Loc;

class SomeElementInfo2Table extends Entity\DataManager
{
	static function getTableName(): string
	{
		return 'exam31_ticket2_someelementinfo2';
	}
	static function getMap(): array
	{
		return array(
			(new Entity\IntegerField('ID'))
				->configurePrimary()
				->configureAutocomplete(),
            (new Entity\IntegerField('ELEMENT_ID'))->configurePrimary(),
            (new Reference('ELEMENT', SomeElement2Table::class,
                Join::on('this.ELEMENT_ID', 'ref.ID')))->configureJoinType('inner'),
			(new Entity\StringField('TITLE'))
				->configureRequired(),
		);
	}

	static function getFieldsDisplayLabel(): array
	{
		$fields = SomeElementInfo2Table::getMap();
		$res = [];
		foreach ($fields as $field)
		{
			$title = $field->getTitle();
			$res[$title] = Loc::getMessage("EXAM31_SOMEELEMENT_{$title}_FIELD_LABEL") ?? $title;
		}
		return $res;
	}
}