<?php

declare(strict_types=1);

namespace App\Reservas;

final class SalaNoDisponible extends ReservaRechazada
{
    public function __construct(
        public readonly Sala $sala,
        public readonly FranjaHoraria $franja,
    ) {
        parent::__construct(sprintf('La sala %s ya está reservada en esa franja', $sala->nombre()));
    }
}
