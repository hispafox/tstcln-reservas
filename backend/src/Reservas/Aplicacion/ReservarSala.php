<?php

declare(strict_types=1);

namespace App\Reservas\Aplicacion;

use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\NotificadorReservas;
use App\Reservas\Dominio\PersonaPenalizada;
use App\Reservas\Dominio\ReglaDeCapacidad;
use App\Reservas\Dominio\ReglaDePenalizacion;
use App\Reservas\Dominio\RepositorioReservas;
use App\Reservas\Dominio\Reserva;
use App\Reservas\Dominio\ReservaNoAdmitida;
use App\Reservas\Dominio\Sala;
use App\Reservas\Dominio\SalaNoDisponible;

final class ReservarSala
{
    public function __construct(
        private RepositorioReservas $reservas,
        private NotificadorReservas $notificador,
        private ReglaDePenalizacion $penalizacion,
        private ReglaDeCapacidad $capacidad = new ReglaDeCapacidad(),
    ) {
    }

    public function ejecutar(PeticionReserva $peticion): Reserva
    {
        if ($this->penalizacion->penaliza($peticion->persona, $peticion->sala)) {
            throw new PersonaPenalizada($peticion->persona);
        }

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
