<?php

declare(strict_types=1);

namespace App\Salas\Dominio;

use App\Reservas\Dominio\FranjaHoraria;

/**
 * Qué parte de las horas de apertura de un periodo está reservada.
 * Las salas abren de lunes a viernes, de 8:00 a 20:00.
 */
final class CalculadoraOcupacion
{
    private const HORA_DE_APERTURA = 8;
    private const HORA_DE_CIERRE = 20;

    /**
     * @param list<FranjaHoraria> $reservadas
     */
    public function porcentaje(FranjaHoraria $periodo, array $reservadas): int
    {
        $abierta = 0;
        $ocupada = 0;

        foreach ($this->jornadas($periodo) as $jornada) {
            $abierta += $jornada->minutos();
            foreach ($reservadas as $reserva) {
                $ocupada += $jornada->minutosEnComunCon($reserva);
            }
        }

        if ($abierta === 0) {
            return 0;
        }

        return (int) round(min($ocupada, $abierta) * 100 / $abierta);
    }

    /**
     * @return list<FranjaHoraria>
     */
    private function jornadas(FranjaHoraria $periodo): array
    {
        $jornadas = [];
        $dia = $periodo->inicio()->setTime(0, 0);

        while ($dia < $periodo->fin()) {
            if ($dia->format('N') < 6) {
                $apertura = max($dia->setTime(self::HORA_DE_APERTURA, 0), $periodo->inicio());
                $cierre = min($dia->setTime(self::HORA_DE_CIERRE, 0), $periodo->fin());
                if ($apertura < $cierre) {
                    $jornadas[] = new FranjaHoraria($apertura, $cierre);
                }
            }
            $dia = $dia->modify('+1 day');
        }

        return $jornadas;
    }
}
