<?php

namespace App\Enums;

enum Currency: string
{
    case COP = 'COP'; // Colombian Peso
    case USD = 'USD'; // US Dollar
    case EUR = 'EUR'; // Euro
    case GBP = 'GBP'; // British Pound
    case JPY = 'JPY'; // Japanese Yen
    case CNY = 'CNY'; // Chinese Yuan

    public function isCOP(): bool
    {
        return $this === self::COP;
    }

    public function isUSD(): bool
    {
        return $this === self::USD;
    }

    public function isEUR(): bool
    {
        return $this === self::EUR;
    }

    public function isGBP(): bool
    {
        return $this === self::GBP;
    }

    public function isJPY(): bool
    {
        return $this === self::JPY;
    }

    public function isCNY(): bool
    {
        return $this === self::CNY;
    }
}