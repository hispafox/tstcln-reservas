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
        return array_values(array_filter(
            $this->reservas,
            fn (Reserva $reserva) => $reserva->sala() === $sala && $reserva->estado() === 'confirmada',
        ));
    }

    public function guardadas(): array
    {
        return $this->reservas;
    }
}
