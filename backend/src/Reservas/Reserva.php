<?php

declare(strict_types=1);

namespace App\Reservas;

class Reserva
{
    private ?int $id = null;
    private string $estado = 'confirmada';

    public function __construct(
        private Sala $sala,
        private FranjaHoraria $franja,
        private string $persona,
        private int $asistentes,
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

    public function asistentes(): int
    {
        return $this->asistentes;
    }

    public function estado(): string
    {
        return $this->estado;
    }

    public function cancelar(bool $tardia): void
    {
        $this->estado = $tardia ? 'cancelada_tarde' : 'cancelada_a_tiempo';
    }
}
