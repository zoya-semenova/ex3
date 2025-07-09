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
use Bitrix\Main\Web\Uri;

class Elements extends CBitrixComponent
{
	protected array $componentVariables = ['ID'];

	function executeComponent(): void
	{
		$componentPage = $this->arParams['SEF_MODE'] === 'Y' ? $this->sefMode() : $this->notSefMode();

		if (!$componentPage)
		{
			$this->process404();
			return;
		}

		$this->includeComponentTemplate($componentPage);
	}

	function sefMode(): string
	{
		$defaultUrlTemplates = [
			'list' => '',
			'detail' => '#ID#/',
		];

		$urlTemplates = CComponentEngine::MakeComponentUrlTemplates(
			$defaultUrlTemplates,
			$this->arParams['SEF_URL_TEMPLATES']
		);

		$variables = [];
		$componentPage = CComponentEngine::ParseComponentPath(
			$this->arParams['SEF_FOLDER'],
			$urlTemplates,
			$variables
		);

		if (!$componentPage)
		{
			$componentPage = 'list';
		}

		$variableAliases = CComponentEngine::MakeComponentVariableAliases([], $this->arParams['VARIABLE_ALIASES']);

		CComponentEngine::InitComponentVariables(
			$componentPage,
			$this->componentVariables,
			$variableAliases,
			$variables
		);

		$this->arResult = [
			'FOLDER' => $this->arParams['SEF_FOLDER'],
			'URL_TEMPLATES' => $urlTemplates,
			'VARIABLES' => $variables,
			'ALIASES' => $variableAliases,
		];

		return $componentPage;
	}

	function notSefMode(): string
	{
		$variableAliases = CComponentEngine::makeComponentVariableAliases([], $this->arParams['VARIABLE_ALIASES']);
		$variables = [];

		CComponentEngine::initComponentVariables(
			false,
			$this->componentVariables,
			$variableAliases,
			$variables
		);

		$request = Application::getInstance()->getContext()->getRequest();
		$uri = new Uri($request->getRequestUri());
		$listUri = $uri->getPath();
		$detailUri = $uri->getPath() . "?{$variableAliases['ID']}=#ID#";

		$this->arResult = [
			'FOLDER' => '',
			'URL_TEMPLATES' => [
				'list' => htmlspecialcharsbx($listUri),
				'detail' => htmlspecialcharsbx($detailUri),
			],
			'VARIABLES' => $variables,
			'ALIASES' => $variableAliases,
		];

		return intval($variables['ID']) > 0 ? 'detail' : 'list';
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
