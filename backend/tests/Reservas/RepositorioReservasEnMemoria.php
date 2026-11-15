<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\RepositorioReservas;
use App\Reservas\Reserva;
use App\Reservas\Sala;

final class RepositorioReservasEnMemoria implements RepositorioReservas
{
    private array $reservas = [];

    public function guardar(Reserva $reserva): void
    {
        $this->reservas[] = $reserva;
    }

    public function confirmadasDeLaSala(Sala $sala): array
    {
        // Por completar en el laboratorio: las reservas de esta sala que estén confirmadas.
        return [];
    }

    public function guardadas(): array
    {
        return $this->reservas;
    }
}
