<?php

namespace App\Service;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

class MoneyManager
{
    public function getFormattedPrice(Money $price): float
    {
        $isoCurrencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($isoCurrencies);
        return (float)$moneyFormatter->format($price);
    }
}