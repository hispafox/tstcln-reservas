<?php

declare(strict_types=1);

namespace App\Reservas\Aplicacion;

use App\Reservas\Dominio\Capacidad;
use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\Sala;

final readonly class PeticionReserva
{
    public function __construct(
        public Sala $sala,
        public FranjaHoraria $franja,
        public string $persona,
        public Capacidad $asistentes,
    ) {
    }
}
