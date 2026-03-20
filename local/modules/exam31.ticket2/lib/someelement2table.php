<?php
namespace Exam31\Ticket2;

use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Localization\Loc;
use Exam31\Ticket1\SomeElementTable;

class SomeElement2Table extends Entity\DataManager
{
	static function getTableName(): string
	{
		return 'exam31_ticket2_someelement2';
	}
	static function getMap(): array
	{
		return array(
			(new Entity\IntegerField('ID'))
				->configurePrimary()
				->configureAutocomplete(),
			(new Entity\BooleanField('ACTIVE'))
				->configureRequired(),
			(new Entity\DatetimeField('DATE_MODIFY'))
				->configureRequired()
				->configureDefaultValue(new DateTime()),
			(new Entity\StringField('TITLE'))
				->configureRequired(),
			new Entity\TextField('TEXT'),
            (new OneToMany('INFO', SomeElementInfo2Table::class, 'ELEMENT'))
            ->configureJoinType('left')
		);
	}

	static function getFieldsDisplayLabel(): array
	{
		$fields = SomeElement2Table::getMap();
		$res = [];
		foreach ($fields as $field)
		{
			$title = $field->getTitle();
			$res[$title] = Loc::getMessage("EXAM31_SOMEELEMENT_{$title}_FIELD_LABEL") ?? $title;
		}
		return $res;
	}
}