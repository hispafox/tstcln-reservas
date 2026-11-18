<?php

declare(strict_types=1);

namespace App\Salas\Dominio;

use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\Sala;

interface AgendaReservas
{
    /**
     * Las franjas de las reservas confirmadas de la sala que tocan el periodo.
     *
     * @return list<FranjaHoraria>
     */
    public function franjasConfirmadas(Sala $sala, FranjaHoraria $periodo): array;
}
