<?php

declare(strict_types=1);

namespace App\Reservas\Infraestructura\Doctrine;

use App\Reservas\Dominio\Capacidad;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

final class TipoCapacidad extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Capacidad
    {
        return $value === null ? null : new Capacidad((int) $value);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?int
    {
        return $value instanceof Capacidad ? $value->valor() : null;
    }

    public function getBindingType(): ParameterType
    {
        return ParameterType::INTEGER;
    }
}
