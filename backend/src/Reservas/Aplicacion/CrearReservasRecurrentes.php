<?php

declare(strict_types=1);

namespace App\Reservas\Aplicacion;

use App\Reservas\Dominio\Capacidad;
use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\NotificadorReservas;
use App\Reservas\Dominio\PersonaPenalizada;
use App\Reservas\Dominio\ReglaDeCapacidad;
use App\Reservas\Dominio\ReglaDePenalizacion;
use App\Reservas\Dominio\RepositorioReservas;
use App\Reservas\Dominio\Reserva;
use App\Reservas\Dominio\ReservaNoAdmitida;
use App\Reservas\Dominio\ResultadoSerie;
use App\Reservas\Dominio\Sala;
use App\Reservas\Dominio\SerieSemanal;
use App\Reservas\Dominio\SerieSinSemanasLibres;

final class CrearReservasRecurrentes
{
    public function __construct(
        private RepositorioReservas $reservas,
        private NotificadorReservas $notificador,
        private ReglaDePenalizacion $penalizacion,
        private ReglaDeCapacidad $capacidad = new ReglaDeCapacidad(),
    ) {
    }

    public function ejecutar(Sala $sala, FranjaHoraria $primera, \DateTimeImmutable $hasta, string $persona, Capacidad $asistentes): ResultadoSerie
    {
        if ($this->penalizacion->penaliza($persona, $sala)) {
            throw new PersonaPenalizada($persona);
        }

        if (!$this->capacidad->admite($sala, $primera, $asistentes)) {
            throw new ReservaNoAdmitida($sala, $asistentes);
        }

        $ocupadas = array_map(fn (Reserva $reserva) => $reserva->franja(), $this->reservas->confirmadasDeLaSala($sala));
        $resultado = (new SerieSemanal($primera, $hasta))->repartir($ocupadas);

        if ($resultado->libres() === []) {
            throw new SerieSinSemanasLibres();
        }

        $creadas = [];
        foreach ($resultado->libres() as $franja) {
            $reserva = new Reserva($sala, $franja, $persona, $asistentes);
            $this->reservas->guardar($reserva);
            $creadas[] = $reserva;
        }

        $this->notificador->serieConfirmada($creadas);

        return $resultado;
    }
}
