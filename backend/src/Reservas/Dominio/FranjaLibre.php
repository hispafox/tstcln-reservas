<?php

declare(strict_types=1);

namespace App\Reservas\Dominio;

final class FranjaLibre extends ReservaRechazada
{
    public function __construct(
        public readonly Sala $sala,
        public readonly FranjaHoraria $franja,
    ) {
        parent::__construct(sprintf('La sala %s está libre en esa franja: puedes reservarla', $sala->nombre()));
    }
}
