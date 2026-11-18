<?php

declare(strict_types=1);

namespace App\Reservas\Dominio;

class Reserva
{
    private ?int $id = null;
    private EstadoReserva $estado = EstadoReserva::Confirmada;

    public function __construct(
        private Sala $sala,
        private FranjaHoraria $franja,
        private string $persona,
        private Capacidad $asistentes,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function sala(): Sala
    {
        return $this->sala;
    }

    public function franja(): FranjaHoraria
    {
        return $this->franja;
    }

    public function persona(): string
    {
        return $this->persona;
    }

    public function asistentes(): Capacidad
    {
        return $this->asistentes;
    }

    public function estado(): EstadoReserva
    {
        return $this->estado;
    }

    public function cancelar(\DateTimeImmutable $ahora): void
    {
        if ($ahora > $this->franja->inicio()->modify('-2 hours')) {
            $this->cancelarTarde();
        } else {
            $this->cancelarATiempo();
        }
    }

    public function cancelarATiempo(): void
    {
        $this->estado = EstadoReserva::CanceladaATiempo;
    }

    public function cancelarTarde(): void
    {
        $this->estado = EstadoReserva::CanceladaTarde;
    }
}
