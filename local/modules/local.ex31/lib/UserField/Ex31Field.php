<?php

namespace Local\Ex31\UserField;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\UserField\Types\BaseType;
use Bitrix\Currency\CurrencyManager;
use Local\Ex31\Collection;
use Local\Ex31\ElementTable;
use RuntimeException;

class Ex31Field extends BaseType
{
    public const USER_TYPE_ID = 'ex31';
    public const RENDER_COMPONENT = 'local.ex31:ex31.field';

    public static array $currencyList = array();

    protected static function getDescription(): array
    {
        return [
            'DESCRIPTION' => Loc::getMessage('B24_ACADEMY.UFTYPE_CURRENCY_FIELD_DESCRIPTION'),
            'BASE_TYPE' => \CUserTypeManager::BASE_TYPE_INT,
        ];
    }

    public static function getDbColumnType(): string
    {
        return 'int(11)';
    }

    public static function prepareSettings(array $userField): array
    {
        return [
            'FORMAT' => $userField['SETTINGS']['FORMAT'] ?: 'Элемент [#ID#] - #TITLE#',
            'LINK' => $userField['SETTINGS']['LINK'] ?: '/invest/info/#ID#/',
        ];
    }

    private static function loadCurrencies(): array
    {
        if (Ex31Field::$currencyList) {
            return Ex31Field::$currencyList;
        }

        $result =  (new Query(
            ElementTable::getEntity()
        ))
            ->setSelect([
                'ID',
                'TITLE',
            ])
        ;

        // echo "<pre>";
        //  print_r( $result->getQuery());

        //  $r = $result->exec();
        // print_r($r->get)
        //  print_r($result->fetchAll());exit;
        //$projects = new Collection();
        $names = [];
        foreach ($result->fetchAll() as $entityArr) {
            $names[$entityArr['ID']] = $entityArr;
        }

        $currencies = [];
        foreach ($names as $currency => $name) {
            $currencies[$currency] = [
                'TITLE' => $name['TITLE'],
                'ID' => $name['ID'],
            ];
        }

        return Ex31Field::$currencyList = $currencies;
    }

    public static function formatCurrency(array $userField, array $currency): string
    {
        $placeholders = array_map(
            static fn(string $placeholder): string => '#' . $placeholder . '#',
            array_keys($currency)
        );

        return str_replace($placeholders, array_values($currency), $userField['SETTINGS']['FORMAT']);
    }

    public static function formatLink(array $userField, array $currency): string
    {
        $placeholders = array_map(
            static fn(string $placeholder): string => '#' . $placeholder . '#',
            array_keys($currency)
        );

        return str_replace($placeholders, array_values($currency), $userField['SETTINGS']['LINK']);
    }

    public static function getFormattedCurrenciesList(array $userField): array
    {
        $formattedCurrencies = [];
        foreach (Ex31Field::loadCurrencies() as $currency => $data) {
            $formattedCurrencies[$currency] = Ex31Field::formatCurrency($userField, $data);
        }

        return $formattedCurrencies;
    }

    public static function onBeforeSave(array $userField, string $value): string
    {
        Ex31Field::validateCurrency($value);

        return $value;
    }

    public static function getDefaultValue(array $userField, array $additionalParameters = [])
    {
        return 0;
    }

    public static function validateCurrency(string $currency): void
    {
        $currencies = Ex31Field::loadCurrencies();

        if (! $currencies[$currency]) {
            throw new RuntimeException('Unknown currency.');
        }
    }

    public static function getCurrencyByName(string $currency): array {
        Ex31Field::validateCurrency($currency);

        $currencies = Ex31Field::loadCurrencies();
        return $currencies[$currency];
    }

}