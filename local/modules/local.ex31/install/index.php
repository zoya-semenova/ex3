<?php

use Local\Ex31\History\HistoryTable;
use Local\Ex31\Integration\Rest\Service;
use Local\Ex31\Integration\UI\SidePanel\RuleInjector;
use Local\Ex31\ElementTable;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\IO\File;
use Bitrix\Main\IO\Path;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\ORM\Entity;
use Bitrix\Main\UrlRewriter;

defined('B_PROLOG_INCLUDED') || die;

final class local_ex31 extends CModule
{
    public function __construct()
    {
        $this->MODULE_ID = 'local.ex31';
        $this->MODULE_NAME = Loc::getMessage('ACADEMY.INVESTMENTPROJECT_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('ACADEMY.INVESTMENTPROJECT_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('ACADEMY.INVESTMENTPROJECT_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('ACADEMY.INVESTMENTPROJECT_PARTNER_URI');

        /** @var array{MODULE_VERSION: string, MODULE_VERSION_DATE: string} $version */
        $version = include __DIR__ . '/version.php';

        $this->MODULE_VERSION = $version['MODULE_VERSION'];
        $this->MODULE_VERSION_DATE = $version['MODULE_VERSION_DATE'];
    }

    /**
     * @throws LoaderException
     */
    public function DoInstall(): void
    {
        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function DoUninstall(): void
    {
        ModuleManager::unregisterModule($this->MODULE_ID);
    }

}