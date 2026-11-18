<?php

declare(strict_types=1);

namespace App\Reservas\Dominio;

class Sala
{
    private const PLAZAS_DE_UNA_SALA_GRANDE = 20;

    private ?int $id = null;

    public function __construct(
        private string $nombre,
        private int $capacidadMaxima,
        private ?int $capacidadMinima = null,
        private array $equipamiento = [],
    ) {}

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

    /**
     * @return list<string>
     */
    public function equipamiento(): array
    {
        return $this->equipamiento;
    }

    public function esGrande(): bool
    {
        return $this->capacidadMaxima >= self::PLAZAS_DE_UNA_SALA_GRANDE;
    }
}
