<?php

declare(strict_types=1);

namespace App\Reservas;

final class ResultadoSerie
{
    /**
     * @param FranjaHoraria[] $libres
     * @param FranjaHoraria[] $descartadas
     */
    public function __construct(
        private array $libres,
        private array $descartadas,
    ) {
    }

    /** @return FranjaHoraria[] */
    public function libres(): array
    {
        return $this->libres;
    }

    /** @return FranjaHoraria[] */
    public function descartadas(): array
    {
        return $this->descartadas;
    }
}
