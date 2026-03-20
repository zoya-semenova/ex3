<?php B_PROLOG_INCLUDED === true || die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Text\HtmlFilter;

use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\ErrorableImplementation;

use Exam31\Ticket2\SomeElement2Table;

class ExamElementsListComponent extends CBitrixComponent implements Errorable
{
	use ErrorableImplementation;

	public function __construct($component = null)
	{
		parent::__construct($component);
		$this->errorCollection = new ErrorCollection();
	}

	public function onPrepareComponentParams($arParams): array
	{
		if (!Loader::includeModule('exam31.ticket2'))
		{
			$this->errorCollection->setError(
				new Error(Loc::getMessage('EXAM31_TICKET_MODULE_NOT_INSTALLED'))
			);
			return $arParams;
		}
		return $arParams;
	}

	private function displayErrors(): void
	{
		foreach ($this->getErrors() as $error)
		{
			ShowError($error->getMessage());
		}
	}

	public function executeComponent(): void
	{
		if ($this->hasErrors())
		{
			$this->displayErrors();
			return;
		}

        $filter = new \Bitrix\Main\ORM\Query\Filter\ConditionTree();

            $filter->where('ELEMENT_ID', $this->arParams['ELEMENT_ID']);

		$this->arResult['ITEMS'] = $this->getSomeElementList($filter);

		$this->includeComponentTemplate();

		global $APPLICATION;
		$APPLICATION->SetTitle(Loc::getMessage('EXAM31_ELEMENTS_LIST_PAGE_TITLE'));
	}


	protected function getSomeElementList($filter): array
	{
		//Демо-данные для грида
		$items = [
			['ID' => 1, 'DATE_MODIFY' => new DateTime(), 'TITLE' => 'TITLE 1', 'TEXT' => 'TEXT 1', 'ACTIVE' => 1],
			['ID' => 2, 'DATE_MODIFY' => new DateTime(), 'TITLE' => 'TITLE <script>alert("2 !!!")</script>', 'TEXT' => 'TEXT 2', 'ACTIVE' => 1],
			['ID' => 3, 'DATE_MODIFY' => new DateTime(), 'TITLE' => 'TITLE 3', 'TEXT' => 'TEXT 3', 'ACTIVE' => 0],
		];

        $items = \Exam31\Ticket2\SomeElementInfo2Table::getList(['filter' => $filter,])->fetchAll();
		$preparedItems = [];
		foreach ($items as $item)
		{
            $item['ID'] = HtmlFilter::encode($item['ID']);
            $item['TITLE'] = HtmlFilter::encode($item['TITLE']);

			$preparedItems[] = $item;
		}
		return $preparedItems;
	}


}