<?php

declare(strict_types=1);

namespace App\Reservas\Dominio;

final class ReservaNoAdmitida extends ReservaRechazada
{
    public function __construct(
        public readonly Sala $sala,
        public readonly Capacidad $asistentes,
    ) {
        parent::__construct(sprintf(
            'La sala %s no admite una reserva de %d asistentes en esa franja',
            $sala->nombre(),
            $asistentes->valor(),
        ));
    }
}
