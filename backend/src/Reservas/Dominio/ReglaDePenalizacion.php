<?php

declare(strict_types=1);

namespace App\Reservas\Dominio;

use Psr\Clock\ClockInterface;

final class ReglaDePenalizacion
{
    public function __construct(
        private RepositorioReservas $reservas,
        private ClockInterface $reloj,
    ) {
    }

    public function penaliza(string $persona, Sala $sala): bool
    {
        if (!$sala->esGrande()) {
            return false;
        }

        $ahora = $this->reloj->now();
        $tardias = $this->reservas->tardiasDeLaPersonaDesde($persona, $ahora->modify('-30 days'));

        return count($tardias) >= 3 && $tardias[2]->franja()->inicio()->modify('+7 days') > $ahora;
    }
}
