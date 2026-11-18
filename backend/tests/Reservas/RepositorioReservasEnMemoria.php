<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Dominio\EstadoReserva;
use App\Reservas\Dominio\RepositorioReservas;
use App\Reservas\Dominio\Reserva;
use App\Reservas\Dominio\Sala;

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
            fn (Reserva $reserva) => $reserva->sala() === $sala && $reserva->estado() === EstadoReserva::Confirmada,
        ));
    }

    public function tardiasDeLaPersonaDesde(string $persona, \DateTimeImmutable $desde): array
    {
        $tardias = array_values(array_filter(
            $this->reservas,
            fn (Reserva $reserva) => $reserva->persona() === $persona
                && $reserva->estado() === EstadoReserva::CanceladaTarde
                && $reserva->franja()->inicio() > $desde,
        ));
        usort($tardias, fn (Reserva $una, Reserva $otra) => $otra->franja()->inicio() <=> $una->franja()->inicio());

        return $tardias;
    }

    public function guardadas(): array
    {
        return $this->reservas;
    }
}
