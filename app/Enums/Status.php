<?php

namespace App\Enums;

enum Status: string
{
    case ACTIVE = 'ACTIVE';
    case HOLD = 'HOLD';
    case PENDING = 'PENDING';
    case EXPIRED = 'EXPIRED';
    case PAID = 'PAID';
    case CANCELLED = 'CANCELLED';

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isHold(): bool
    {
        return $this === self::HOLD;
    }

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isExpired(): bool
    {
        return $this === self::EXPIRED;
    }

    public function isPaid(): bool
    {
        return $this === self::PAID;
    }

    public function isCancelled(): bool
    {
        return $this === self::CANCELLED;
    }
}
