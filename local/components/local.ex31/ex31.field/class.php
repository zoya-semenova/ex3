<?php

use Local\Ex31\UserField\Ex31Field;
use Bitrix\Main\Component\BaseUfComponent;
use Bitrix\Main\Loader;

defined('B_PROLOG_INCLUDED') || die;


class Ex31FieldComponent extends BaseUfComponent
{
    public function __construct($component = null)
    {
        Loader::requireModule('local.ex31');

        parent::__construct($component);
    }

    public function getAvailableCurrencies(): array
    {
        return Ex31Field::getFormattedCurrenciesList($this->userField);
    }

    public function formatCurrency(string $currency): string
    {
        return Ex31Field::formatCurrency($this->userField, Ex31Field::getCurrencyByName($currency));
    }

    public function formatLink(string $currency): string
    {
        return Ex31Field::formatLink($this->userField, Ex31Field::getCurrencyByName($currency));
    }

    protected static function getUserTypeId(): string
    {
        return Ex31Field::USER_TYPE_ID;
    }
}