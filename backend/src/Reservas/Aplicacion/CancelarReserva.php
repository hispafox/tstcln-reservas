<?php

declare(strict_types=1);

namespace App\Reservas\Aplicacion;

use App\Reservas\Dominio\RepositorioReservas;
use App\Reservas\Dominio\Reserva;
use App\Reservas\Dominio\ReservaNoEncontrada;
use Psr\Clock\ClockInterface;

final class CancelarReserva
{
    public function __construct(
        private RepositorioReservas $reservas,
        private ClockInterface $reloj,
    ) {
    }

    public function ejecutar(int $id): Reserva
    {
        $reserva = $this->reservas->conId($id) ?? throw new ReservaNoEncontrada($id);

        $reserva->cancelar($this->reloj->now());
        $this->reservas->guardar($reserva);

        return $reserva;
    }
}
