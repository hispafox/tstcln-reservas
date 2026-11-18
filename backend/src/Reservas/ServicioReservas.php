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

    public function reservar(PeticionReserva $peticion): Reserva
    {
        if (!$this->capacidad->admite($peticion->sala, $peticion->franja, $peticion->asistentes)) {
            throw new ReservaNoAdmitida($peticion->sala, $peticion->asistentes);
        }

        $this->comprobarDisponibilidad($peticion->sala, $peticion->franja);

        $reserva = new Reserva($peticion->sala, $peticion->franja, $peticion->persona, $peticion->asistentes);
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
