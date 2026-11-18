<?php

declare(strict_types=1);

namespace App\Reservas\Dominio;

enum EstadoReserva: string
{
    case Confirmada = 'confirmada';
    case CanceladaATiempo = 'cancelada_a_tiempo';
    case CanceladaTarde = 'cancelada_tarde';
}
