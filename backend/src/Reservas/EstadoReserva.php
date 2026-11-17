<?php

declare(strict_types=1);

namespace App\Reservas;

enum EstadoReserva: string
{
    case Confirmada = 'confirmada';
    case CanceladaATiempo = 'cancelada_a_tiempo';
    case CanceladaTarde = 'cancelada_tarde';
}
