<?php

declare(strict_types=1);

namespace App\Reservas\Dominio;

final class ReglaDeCapacidad
{
    public function admite(Sala $sala, FranjaHoraria $franja, Capacidad $asistentes): bool
    {
        $cabe = $asistentes->cabeEntre($sala->capacidadMinima() ?? 1, $sala->capacidadMaxima());

        if (!$sala->esGrande()) {
            return $cabe;
        }

        return $cabe
            && self::esMinutoDeBloque($franja->inicio())
            && self::esMinutoDeBloque($franja->fin());
    }

    private static function esMinutoDeBloque(\DateTimeImmutable $momento): bool
    {
        return in_array((int) $momento->format('i'), [0, 30], true);
    }
}
