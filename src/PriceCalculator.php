<?php
declare(strict_types=1);

namespace Otus\MoneyCalc;

use Money\Currency;
use Money\Money;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;

class PriceCalculator
{
    public function add(string $amount1, string $amount2, string $currencyCode = 'RUB'): string
    {
        $currency = new Currency($currencyCode);
        $money1 = new Money($amount1, $currency);
        $money2 = new Money($amount2, $currency);

        $result = $money1->add($money2);

        $currencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($currencies);

        return $moneyFormatter->format($result);
    }
}
