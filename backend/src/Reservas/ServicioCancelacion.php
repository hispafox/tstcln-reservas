<?php

declare(strict_types=1);

namespace App\Reservas;

use Psr\Clock\ClockInterface;

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

        if ($this->reloj->now() > $limite) {
            $reserva->cancelarTarde();
        } else {
            $reserva->cancelarATiempo();
        }

        $this->reservas->guardar($reserva);
    }
}
