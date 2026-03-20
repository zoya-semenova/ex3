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
	protected const DEFAULT_PAGE_SIZE = 10;
	protected const GRID_ID = 'EXAM31_GRID_ELEMENT';
    protected const FILTER_ID = 'EXAM31_FILTER_ELEMENT';

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

		$arParams['ELEMENT_COUNT'] = (int) $arParams['ELEMENT_COUNT'];
		if ($arParams['ELEMENT_COUNT'] <= 0)
		{
			$arParams['ELEMENT_COUNT'] = static::DEFAULT_PAGE_SIZE;
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

        $options = new \Bitrix\Main\Grid\Options(static::GRID_ID);
        $sort = $options->getSorting(['ID' => 'DESC']);

        $filterOptions = new \Bitrix\Main\UI\Filter\Options(static::FILTER_ID);
        $rawValue = $filterOptions->getFilter();
        $filter = new \Bitrix\Main\ORM\Query\Filter\ConditionTree();
        if (isset($rawValue['TITLE'])) {
            $filter->whereLike('TITLE', '%'.$rawValue['TITLE'].'%');
        }

        $navigation = new \Bitrix\Main\UI\PageNavigation('n');
        $navigation->setPageSize(static::DEFAULT_PAGE_SIZE);
        $navigation->setRecordCount($this->count($filter));
        $navigation->initFromUri();

		$this->arResult['ITEMS'] = $this->getSomeElementList($filter, $sort['sort'], $navigation->getPageSize(),
            ($navigation->getCurrentPage()-1)*$navigation->getPageSize());
		$this->arResult['grid'] = $this->prepareGrid($this->arResult['ITEMS'], $navigation);
        $this->arResult['filter'] = [

            'FILTER' => $this->prepareFilter(),
            'FILTER_ID' => static::FILTER_ID,
            'GRID_ID' => static::GRID_ID,
            'ENABLE_FILTER' => true,
            'DISABLE_SEARCH' => true,
        ];
        $this->arResult['toolbar'] = [
            'buttons' => [
                new \Bitrix\UI\Buttons\CreateButton([
                    'text' => 'Добавить',
                    'link' => $this->getDetailPageUrl(0)
                ])
            ]
        ];

		$this->includeComponentTemplate();

		global $APPLICATION;
		$APPLICATION->SetTitle(Loc::getMessage('EXAM31_ELEMENTS_LIST_PAGE_TITLE'));
	}

    protected function count($filter): int
    {
        return SomeElement2Table::getCount($filter);
    }

	protected function getSomeElementList($filter, $sort, $limit, $offset): array
	{
		//Демо-данные для грида
		$items = [
			['ID' => 1, 'DATE_MODIFY' => new DateTime(), 'TITLE' => 'TITLE 1', 'TEXT' => 'TEXT 1', 'ACTIVE' => 1],
			['ID' => 2, 'DATE_MODIFY' => new DateTime(), 'TITLE' => 'TITLE <script>alert("2 !!!")</script>', 'TEXT' => 'TEXT 2', 'ACTIVE' => 1],
			['ID' => 3, 'DATE_MODIFY' => new DateTime(), 'TITLE' => 'TITLE 3', 'TEXT' => 'TEXT 3', 'ACTIVE' => 0],
		];

        $items = SomeElement2Table::getList(['select' => ['*', 'cnt'], 'filter' => $filter,
            'order' => $sort, 'offset' => $offset,
            'limit' => $limit,
            'runtime' => [
                'cnt' => [
                    'data_type' => 'integer',
                    'expression' => ['count(%s)', 'INFO.ID']
                ]
            ]])->fetchAll();
		$preparedItems = [];
		foreach ($items as $item)
		{
			$item['DETAIL_URL'] = $this->getDetailPageUrl($item['ID']);
            $item['INFO_URL'] = $this->getInfoPageUrl($item['ID']);
			$item['DATE_MODIFY'] = $item['DATE_MODIFY'] instanceof DateTime
				? $item['DATE_MODIFY']->toString()
				: null;

			$preparedItems[] = $item;
		}
		return $preparedItems;
	}

	protected function prepareGrid($items, $navigation): array
	{
		return [
			'GRID_ID' => static::GRID_ID,
			'COLUMNS' => $this->getGridColums(),
			'ROWS' => $this->getGridRows($items),
            'NAV_OBJECT' => $navigation,
			'TOTAL_ROWS_COUNT' => count($items),
			'SHOW_ROW_CHECKBOXES' => false,
			'SHOW_SELECTED_COUNTER' => false,
			'AJAX_MODE' => 'Y',
			'AJAX_OPTION_JUMP' => 'N',
			'AJAX_OPTION_HISTORY' => 'N',
		];
	}

    protected function prepareFilter()
    {
        $fieldsLabel = SomeElement2Table::getFieldsDisplayLabel();
        return [
                   ['id' => 'TITLE', 'default' => true, 'name' => $fieldsLabel['TITLE'] ?? 'TITLE'],
           ];
    }

	protected function getGridColums(): array
	{
		$fieldsLabel = SomeElement2Table::getFieldsDisplayLabel();
		return [
			['id' => 'ACTIVE', 'default' => true, 'name' => $fieldsLabel['ACTIVE'] ?? 'ACTIVE'],
			['id' => 'ID', 'default' => true, 'name' => $fieldsLabel['ID'] ?? 'ID', 'sort' => 'ID'],
			['id' => 'DATE_MODIFY', 'default' => true, 'name' => $fieldsLabel['DATE_MODIFY'] ?? 'DATE_MODIFY'],
			['id' => 'TITLE', 'default' => true, 'name' => $fieldsLabel['TITLE'] ?? 'TITLE'],
			['id' => 'TEXT', 'default' => true, 'name' => $fieldsLabel['TEXT'] ?? 'TEXT'],
			['id' => 'DETAIL', 'default' => true, 'name' => Loc::getMessage('EXAM31_ELEMENTS_LIST_GRIG_COLUMN_DETAIL_NAME')],
            ['id' => 'INFO', 'default' => true, 'name' => Loc::getMessage('EXAM31_ELEMENTS_LIST_GRIG_COLUMN_INFO_NAME')],

		];
	}
	protected function getGridRows(array $items): array
	{
		if (empty($items))
		{
			return [];
		}

		$rows = [];
		foreach ($items as $key => $item)
		{
			$rows[$key] = [
				'id' => $item["ID"],
                'actions' => $this->prepareRowActions($item),
				'columns' => [
					'ID' => $item["ID"],
					'DATE_MODIFY' => $item["DATE_MODIFY"],
					'TITLE' => $item["TITLE"],
					'TEXT' => $item["TEXT"],
					'ACTIVE' => $item["ACTIVE"],
					'DETAIL' => $this->getDetailHTMLLink($item["DETAIL_URL"]),
                    'INFO' => $this->getInfoHTMLLink($item["INFO_URL"], $item['cnt']),
				]
			];
		}
		return $rows;
	}

    private function prepareRowActions($item): array
    {
        return [
            [
                'text' => Loc::getMessage('EXAM31_ELEMENTS_LIST_GRIG_COLUMN_DETAIL_NAME'),
                'default' => false,
                'href' => CComponentEngine::makePathFromTemplate(
                    $this->arParams['DETAIL_PAGE_URL'],
                    ['ELEMENT_ID' => $item['ID']]
                )
            ],
            [
                'text' => Loc::getMessage('EXAM31_ELEMENTS_LIST_GRIG_COLUMN_INFO_NAME'),
                'default' => false,
                'href' => CComponentEngine::makePathFromTemplate(
                    $this->arParams['INFO_PAGE_URL'],
                    ['ELEMENT_ID' => $item['ID']]
                )
            ],
        ];
    }

	protected function getDetailPageUrl(int $id): string
	{
		return str_replace('#ELEMENT_ID#', $id, $this->arParams['DETAIL_PAGE_URL']);
	}

    protected function getInfoPageUrl(int $id): string
    {
        return str_replace('#ELEMENT_ID#', $id, $this->arParams['INFO_PAGE_URL']);
    }
	protected function getDetailHTMLLink(string $detail_url): string
	{
		return "<a href=\"" . $detail_url . "\">" . Loc::getMessage('EXAM31_ELEMENTS_LIST_GRIG_COLUMN_DETAIL_NAME') . "</a>";
	}

    protected function getInfoHTMLLink(string $detail_url, $count): string
    {
        return "<a href=\"" . $detail_url . "\">" . Loc::getMessage('EXAM31_ELEMENTS_LIST_GRIG_COLUMN_INFO_NAME') . ' ' . $count . "</a>";
    }
}