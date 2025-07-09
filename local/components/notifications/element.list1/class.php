<?php B_PROLOG_INCLUDED === true || die();


/**
 * НЕ РАЗМЕЩАЙТЕ ЭТОТ КОД В ДЕЙСТВУЩИХ ПРОЕКТАХ И ПУБЛИЧНЫХ ВЕБ-СЕРВЕРАХ!
 * Здесь специально подготовленные примеры с грубыми ошибками в безопасности,
 * которые будут продемонстрированы и исправлены в уроке.
 * Никогда не размещайте эти материалы в действующих проектах
 * и тем более на публичных веб-серверах,
 * используйте только для обучения в рамках вашего личного
 * учебного проекта на локальном веб-сервере.
 */


use Bitrix\Main\Application;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UI\PageNavigation;
use MyCompany\Custom\Notification\NotificationTable;

class ElementList extends CBitrixComponent
{
	public function onPrepareComponentParams($arParams): array
	{
		$arParams['ELEMENT_COUNT'] = (int)$arParams['ELEMENT_COUNT'];
		if ($arParams['ELEMENT_COUNT'] <= 0)
		{
			$arParams['ELEMENT_COUNT'] = 5;
		}

		return $arParams;
	}

	function executeComponent(): void
	{
		global $USER;
		$userId = $USER->getId();
		if ($userId <= 0)
		{
			return;
		}

		$prefilter = $this->getPreFilter();
		$this->arResult['NAV'] = $this->getPageNavigationObject($this->arParams['ELEMENT_COUNT']);
		$this->arResult['ITEMS'] = $this->getUserNotifications($userId, $this->arResult['NAV'], $prefilter);
		$this->arResult['DISPLAY_VALUES'] = $this->getDisplayValues($this->arResult['ITEMS']);
		$this->arResult['PREFILTERS'] = ['all', 'unread', 'important'];

		$this->includeComponentTemplate();
	}

	protected function getPageNavigationObject(int $pageSize): PageNavigation
	{
		$nav = new PageNavigation('navigation');
		$nav->allowAllRecords(false)
			->setPageSize($pageSize)
			->initFromUri();

		return $nav;
	}

	protected function getUserNotifications(int $userId, PageNavigation $nav, array $prefilter = array()): array
	{
		$order = ['DATE_CREATE' => 'DESC', 'ID' => 'DESC'];
		$select = ['ID', 'DATE_CREATE', 'TITLE', 'USER_ID', 'IS_READ', 'IS_IMPORTANT'];
		$filter = ['=USER_ID' => $userId];
		$filter = array_merge($prefilter, $filter);

		$result = \Local\Ex31\ElementTable::getList([
			'order' => $order,
			'filter' => $filter,
			'select' => $select,
			'offset' => $nav->getOffset(),
			'limit' => $nav->getLimit(),
			'count_total' => true,
		]);

		$items = [];
		while ($item = $result->fetch())
		{
			$items[$item['ID']] = $item;
		}

		$nav->setRecordCount($result->getCount());

		return $items;
	}

	/**
	 * !!! В метод требуется добавить приведение данных
	 * к безопасному для вывода на экран виду !!!
	 */
	protected function getDisplayValues(array $items): array
	{
		if (empty($items))
		{
			return [];
		}

		foreach ($items as $id => $item)
		{
			$item['DETAIL_PAGE_URL'] = $this->getDetailPageUrl($id);
			$item['DATE_CREATE'] = $item['DATE_CREATE'] instanceof DateTime
				? $item['DATE_CREATE']->toString()
				: null;

			$items[$id] = $item;
		}

		return $items;
	}

	protected function getPreFilter(): array
	{
		$request = Application::getInstance()->getContext()->getRequest();

		return match ($request->get('prefilter'))
		{
			'important' => ['=IS_IMPORTANT' => true],
			'unread' => ['=IS_READ' => false],
			default => [],
		};
	}

	protected function getDetailPageUrl(int $id): string
	{
		return str_replace('#ID#', $id, $this->arParams['DETAIL_URL']);
	}
}
