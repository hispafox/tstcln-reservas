<?php

declare(strict_types=1);

namespace App\Reservas;

use Symfony\Component\Clock\ClockInterface;

final class ServicioCancelacion
{
    public function __construct(
        private RepositorioReservas $reservas,
        private ClockInterface $reloj,
    ) {
    }

    public function cancelar(Reserva $reserva): void
    {
        $limite = $reserva->franja()->inicio()->modify('-2 hours');

        $reserva->cancelar(tardia: $this->reloj->now() > $limite);
        $this->reservas->guardar($reserva);
    }
}
