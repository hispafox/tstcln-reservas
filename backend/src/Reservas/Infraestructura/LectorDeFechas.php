<?php

declare(strict_types=1);

namespace App\Reservas\Infraestructura;

final class LectorDeFechas
{
    public function __construct(
        private \DateTimeZone $zona,
    ) {}

    public function leer(string $texto): \DateTimeImmutable
    {
        return (new \DateTimeImmutable($texto))->setTimezone($this->zona);
    }
}
