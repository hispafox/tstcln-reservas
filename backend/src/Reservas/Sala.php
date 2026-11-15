<?php

declare(strict_types=1);

namespace App\Reservas;

class Sala
{
    private ?int $id = null;

    public function __construct(
        private string $nombre,
        private int $capacidadMaxima,
        private ?int $capacidadMinima = null,
    ) {
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function nombre(): string
    {
        return $this->nombre;
    }

    public function capacidadMaxima(): int
    {
        return $this->capacidadMaxima;
    }

    public function capacidadMinima(): ?int
    {
        return $this->capacidadMinima;
    }
}
