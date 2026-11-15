<?php

declare(strict_types=1);

namespace App\Reservas;

final class HorarioLaborable
{
    public function admite(FranjaHoraria $franja): bool
    {
        if ($franja->inicio()->format('H:i') < '08:00') {
            return false;
        }

        if ($franja->fin()->format('H:i') > '20:00') {
            return false;
        }

        if ($franja->inicio()->format('N') >= 6) {
            return false;
        }

        return true;
    }
}
