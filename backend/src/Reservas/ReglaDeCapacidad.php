<?php

declare(strict_types=1);

namespace App\Reservas;

final class ReglaDeCapacidad
{
    public function admite(Sala $sala, FranjaHoraria $franja, int $asistentes): bool
    {
        $minimo = $sala->capacidadMinima() ?? 1;

        return $asistentes >= $minimo && $asistentes <= $sala->capacidadMaxima();
    }
}
