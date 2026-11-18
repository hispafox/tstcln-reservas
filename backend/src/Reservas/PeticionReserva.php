<?php

declare(strict_types=1);

namespace App\Reservas;

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
