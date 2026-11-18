<?php

declare(strict_types=1);

namespace App\Reservas\Aplicacion;

use App\Reservas\Dominio\NotificadorReservas;
use App\Reservas\Dominio\PersonaPenalizada;
use App\Reservas\Dominio\ReglaDeCapacidad;
use App\Reservas\Dominio\ReglaDePenalizacion;
use App\Reservas\Dominio\RepositorioReservas;
use App\Reservas\Dominio\RepositorioSalas;
use App\Reservas\Dominio\Reserva;
use App\Reservas\Dominio\ReservaNoAdmitida;
use App\Reservas\Dominio\ResultadoSerie;
use App\Reservas\Dominio\SalaNoEncontrada;
use App\Reservas\Dominio\SerieSemanal;
use App\Reservas\Dominio\SerieSinSemanasLibres;

final class CrearReservasRecurrentes
{
    public function __construct(
        private RepositorioSalas $salas,
        private RepositorioReservas $reservas,
        private NotificadorReservas $notificador,
        private ReglaDePenalizacion $penalizacion,
        private ReglaDeCapacidad $capacidad = new ReglaDeCapacidad(),
    ) {
    }

    public function ejecutar(PeticionReserva $peticion, \DateTimeImmutable $hasta): ResultadoSerie
    {
        $sala = $this->salas->conId($peticion->sala) ?? throw new SalaNoEncontrada($peticion->sala);

        if ($this->penalizacion->penaliza($peticion->persona, $sala)) {
            throw new PersonaPenalizada($peticion->persona);
        }

        if (!$this->capacidad->admite($sala, $peticion->franja, $peticion->asistentes)) {
            throw new ReservaNoAdmitida($sala, $peticion->asistentes);
        }

        $ocupadas = array_map(fn (Reserva $reserva) => $reserva->franja(), $this->reservas->confirmadasDeLaSala($sala));
        $resultado = (new SerieSemanal($peticion->franja, $hasta))->repartir($ocupadas);

        if ($resultado->libres() === []) {
            throw new SerieSinSemanasLibres();
        }

        $creadas = [];
        foreach ($resultado->libres() as $franja) {
            $reserva = new Reserva($sala, $franja, $peticion->persona, $peticion->asistentes);
            $this->reservas->guardar($reserva);
            $creadas[] = $reserva;
        }

        $this->notificador->serieConfirmada($creadas);

        return $resultado;
    }
}
