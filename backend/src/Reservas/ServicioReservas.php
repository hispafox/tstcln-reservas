<?php

declare(strict_types=1);

namespace App\Reservas;

class ServicioReservas
{
    public function __construct(
        private RepositorioReservas $reservas,
        private NotificadorReservas $notificador,
    ) {
    }

    public function reservar(Sala $sala, FranjaHoraria $franja, string $persona, int $asistentes): Reserva
    {
        $this->comprobarDisponibilidad($sala, $franja);

        $reserva = new Reserva($sala, $franja, $persona, $asistentes);
        $this->reservas->guardar($reserva);
        $this->notificador->reservaConfirmada($reserva);

        return $reserva;
    }

    protected function comprobarDisponibilidad(Sala $sala, FranjaHoraria $franja): void
    {
        foreach ($this->reservas->confirmadasDeLaSala($sala) as $existente) {
            if ($existente->franja()->seSolapaCon($franja)) {
                throw new \DomainException('La sala ya está reservada en esa franja');
            }
        }
    }
}
