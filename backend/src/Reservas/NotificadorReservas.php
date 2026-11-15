<?php

declare(strict_types=1);

namespace App\Reservas;

interface NotificadorReservas
{
    public function reservaConfirmada(Reserva $reserva): void;

    /** @param Reserva[] $reservas */
    public function serieConfirmada(array $reservas): void;
}
