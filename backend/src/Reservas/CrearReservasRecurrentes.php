<?php

declare(strict_types=1);

namespace App\Reservas;

final class CrearReservasRecurrentes
{
    public function __construct(
        private RepositorioReservas $reservas,
        private NotificadorReservas $notificador,
        private ReglaDeCapacidad $capacidad = new ReglaDeCapacidad(),
    ) {
    }

    public function ejecutar(Sala $sala, FranjaHoraria $primera, \DateTimeImmutable $hasta, string $persona, Capacidad $asistentes): ResultadoSerie
    {
        if (!$this->capacidad->admite($sala, $primera, $asistentes)) {
            throw new ReservaNoAdmitida($sala, $asistentes);
        }

        $ocupadas = array_map(fn (Reserva $reserva) => $reserva->franja(), $this->reservas->confirmadasDeLaSala($sala));
        $resultado = (new SerieSemanal($primera, $hasta))->repartir($ocupadas);

        if ($resultado->libres() === []) {
            throw new SerieSinSemanasLibres();
        }

        $creadas = [];
        foreach ($resultado->libres() as $franja) {
            $reserva = new Reserva($sala, $franja, $persona, $asistentes);
            $this->reservas->guardar($reserva);
            $creadas[] = $reserva;
        }

        $this->notificador->serieConfirmada($creadas);

        return $resultado;
    }
}
