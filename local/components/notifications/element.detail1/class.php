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
use Bitrix\Main\FileTable;
use Bitrix\Main\Text\HtmlConverter;
use Bitrix\Main\Type\DateTime;
use MyCompany\Custom\Notification\NotificationFileTable;
use MyCompany\Custom\Notification\NotificationTable;

class ElementDetail extends CBitrixComponent
{
	function onPrepareComponentParams($arParams)
	{
		$arParams['NOTIFICATION_ID'] = (int)$arParams['NOTIFICATION_ID'];
		return $arParams;
	}

	function executeComponent(): void
	{
		global $USER, $APPLICATION;

		$userId = $USER->getId();
		if ($userId <= 0)
		{
			return;
		}

		$notificationId = (int)$this->arParams['NOTIFICATION_ID'];
		if ($notificationId <= 0)
		{
			$this->process404();
			return;
		}

		$this->arResult['NOTIFICATION'] = $this->getNotification($notificationId);
		if (empty($this->arResult['NOTIFICATION']))
		{
			$this->process404();
			return;
		}

		/*
		 * !!! Требуется проверка токена защиты от CSRF-атаки перед выполнением действий !!!
		 */
		if ($this->isSetImportantRequest())
		{
			$connection = Application::getConnection();
			$request = Application::getInstance()->getContext()->getRequest();

			/*
			 * !!! Требуется защита от SQL-инъекции !!!
			 */
			$value = $request->getPost('important');
			$connection->query("UPDATE mycmp_notification SET IS_IMPORTANT = $value WHERE ID = $notificationId");
			LocalRedirect($APPLICATION->GetCurPage());
		}

		$this->arResult['DISPLAY_VALUES'] = $this->getDisplayValues($this->arResult['NOTIFICATION']);
		$this->arResult['LIST_URL'] = htmlspecialcharsbx($this->arParams['LIST_URL']);

		$this->includeComponentTemplate();

		if (!$this->arResult['NOTIFICATION']['IS_READ'])
		{
			NotificationTable::update($notificationId, ['IS_READ' => true]);
		}

		if ($this->arResult['DISPLAY_VALUES']['TITLE'])
		{
			$APPLICATION->SetTitle($this->arResult['DISPLAY_VALUES']['TITLE']);
		}
	}

	/**
	 * !!! Требуется добавить проверку прав доступа пользователя к уведомлению с заданным ID !!!
	 */
	protected function getNotification(int $notificationId): array
	{
		if ($notificationId <= 0)
		{
			return [];
		}

		$filter = [
			'=ID' => $notificationId,
		];

		$select = [
			'ID',
			'DATE_CREATE',
			'TITLE',
			'MESSAGE',
			'USER_ID',
			'IS_READ',
			'IS_IMPORTANT',
		];

		$notification = \Local\Ex31\ElementTable::getList([
			"select" => $select,
			"filter" => $filter,
		])->fetchObject();

		if (!$notification)
		{
			return [];
		}

		$result = [
			'ID' => $notification->getId(),
			'DATE_CREATE' => $notification->get('DATE_CREATE'),
			'TITLE' => $notification->get('TITLE'),
			'MESSAGE' => $notification->get('MESSAGE'),
			'USER_ID' => $notification->get('USER_ID'),
			'IS_READ' => $notification->get('IS_READ'),
			'IS_IMPORTANT' => $notification->get('IS_IMPORTANT'),
		];

		$queryResult = \Local\Ex31\History\ElementInfoTable::getList([
			'filter' => ['NOTIFICATION_ID' => $result['ID']],
			'select' => ['FILE_ID'],
		]);

		/*
		 * ! Неоправданные запросы в цикле !
		 */
		while ($row = $queryResult->fetch())
		{
			$file = FileTable::getList([
				'filter' => ['ID' => $row['FILE_ID']],
				'select' => ['ID', 'HEIGHT', 'WIDTH', 'FILE_SIZE', 'FILE_NAME', 'ORIGINAL_NAME', 'CONTENT_TYPE', 'SUBDIR'],
			])->fetch();

			$result['FILES'][$file['ID']] = $file;
		}

		return $result;
	}

	protected function getDisplayValues(array $item): array
	{
		$converter = new HtmlConverter();

		$item['DATE_CREATE'] = $item['DATE_CREATE'] instanceof DateTime
			? $item['DATE_CREATE']->toString()
			: null;

		foreach ($item as $field => $value)
		{
			if (!is_array($value))
			{
				$item[$field] = $converter->encode($value);
			}
		}

		$item['MESSAGE'] = nl2br($item['MESSAGE']);

		foreach ($item['FILES'] as $id => $file)
		{
			$item['FILES'][$id]['SRC'] = CFile::GetFileSRC($file);

			foreach ($file as $field => $value)
			{
				if (!is_array($value))
				{
					$item['FILES'][$id][$field] = $converter->encode($value);
				}
			}
		}

		return $item;
	}

	protected function isSetImportantRequest(): bool
	{
		$request = Application::getInstance()->getContext()->getRequest();
		return $request->isPost() && !is_null($request->getPost('important'));
	}

	protected function process404()
	{
		define('ERROR_404', 'Y');
		CHTTP::setStatus('404 Not Found');

		global $APPLICATION;
		$APPLICATION->RestartWorkarea();
		require_once(Application::getDocumentRoot() . '/404.php');
	}
}
