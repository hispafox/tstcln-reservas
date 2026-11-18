<?php

declare(strict_types=1);

namespace App\Reservas\Aplicacion;

use App\Reservas\Dominio\Capacidad;
use App\Reservas\Dominio\FranjaHoraria;

final readonly class PeticionReserva
{
    public function __construct(
        public int $sala,
        public FranjaHoraria $franja,
        public string $persona,
        public Capacidad $asistentes,
    ) {}
}
