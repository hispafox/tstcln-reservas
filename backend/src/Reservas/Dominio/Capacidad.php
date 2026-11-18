<?php

declare(strict_types=1);

namespace App\Reservas\Dominio;

final readonly class Capacidad
{
    public function __construct(private int $valor)
    {
        if ($valor < 1) {
            throw new DatoNoValido('Una reserva necesita al menos un asistente');
        }
    }

    public function valor(): int
    {
        return $this->valor;
    }

    public function cabeEntre(int $minimo, int $maximo): bool
    {
        return $this->valor >= $minimo && $this->valor <= $maximo;
    }
}
