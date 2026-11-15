<?php

declare(strict_types=1);

namespace App\Reservas;

final class ServicioCancelacion
{
    public function __construct(
        private RepositorioReservas $reservas,
    ) {
    }

    public function cancelar(Reserva $reserva, \DateTimeImmutable $momento): void
    {
        $limite = $reserva->franja()->inicio()->modify('-2 hours');

        $reserva->cancelar(tardia: $momento > $limite);
        $this->reservas->guardar($reserva);
    }
}
