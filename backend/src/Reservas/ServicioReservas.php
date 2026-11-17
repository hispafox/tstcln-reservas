<?php

declare(strict_types=1);

namespace App\Reservas;

final class ServicioReservas
{
    public function __construct(
        private RepositorioReservas $reservas,
        private NotificadorReservas $notificador,
        private ReglaDeCapacidad $capacidad = new ReglaDeCapacidad(),
    ) {
    }

    public function reservar(Sala $sala, FranjaHoraria $franja, string $persona, Capacidad $asistentes): Reserva
    {
        if (!$this->capacidad->admite($sala, $franja, $asistentes)) {
            throw new ReservaNoAdmitida($sala, $asistentes);
        }

        $this->comprobarDisponibilidad($sala, $franja);

        $reserva = new Reserva($sala, $franja, $persona, $asistentes);
        $this->reservas->guardar($reserva);
        $this->notificador->reservaConfirmada($reserva);

        return $reserva;
    }

    private function comprobarDisponibilidad(Sala $sala, FranjaHoraria $franja): void
    {
        foreach ($this->reservas->confirmadasDeLaSala($sala) as $existente) {
            if ($existente->franja()->seSolapaCon($franja)) {
                throw new SalaNoDisponible($sala, $franja);
            }
        }
    }
}
