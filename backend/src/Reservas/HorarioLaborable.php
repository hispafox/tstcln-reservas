<?php

declare(strict_types=1);

namespace App\Reservas;

final class HorarioLaborable
{
    private const HORA_DE_APERTURA = 8;
    private const HORA_DE_CIERRE = 20;

    public function admite(FranjaHoraria $franja): bool
    {
        $jornada = $this->jornadaDelDia($franja->inicio());

        return $jornada !== null && $jornada->contiene($franja);
    }

    private function jornadaDelDia(\DateTimeImmutable $dia): ?FranjaHoraria
    {
        if ($dia->format('N') >= 6) {
            return null;
        }

        return new FranjaHoraria(
            $dia->setTime(self::HORA_DE_APERTURA, 0),
            $dia->setTime(self::HORA_DE_CIERRE, 0),
        );
    }
}
