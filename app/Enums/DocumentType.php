<?php

namespace App\Enums;

enum DocumentType: string
{
    case CC = 'CC'; // Cédula de Ciudadanía
    case CE = 'CE'; // Cédula de Extranjería
    case TI = 'TI'; // Tarjeta de Identidad
    case PASSPORT = 'PASSPORT'; // Pasaporte
    case NIT = 'NIT'; // Número de Identificación Tributaria
    case RUT = 'RUT'; // Registro Único Tributario

    public function isCC(): bool
    {
        return $this === self::CC;
    }

    public function isCE(): bool
    {
        return $this === self::CE;
    }

    public function isTI(): bool
    {
        return $this === self::TI;
    }

    public function isPassport(): bool
    {
        return $this === self::PASSPORT;
    }

    public function isNIT(): bool
    {
        return $this === self::NIT;
    }

    public function isRUT(): bool
    {
        return $this === self::RUT;
    }
}