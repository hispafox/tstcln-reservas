<?php

declare(strict_types=1);

namespace App\Reservas;

final readonly class FranjaHoraria
{
    public function __construct(
        private \DateTimeImmutable $inicio,
        private \DateTimeImmutable $fin,
    ) {
        if ($fin <= $inicio) {
            throw new DatoNoValido('Una franja tiene que terminar después de empezar');
        }
    }

    public function inicio(): \DateTimeImmutable
    {
        return $this->inicio;
    }

    public function fin(): \DateTimeImmutable
    {
        return $this->fin;
    }

    public function seSolapaCon(self $otra): bool
    {
        return $this->inicio < $otra->fin && $otra->inicio < $this->fin;
    }

    public function contiene(self $otra): bool
    {
        return $this->inicio <= $otra->inicio && $otra->fin <= $this->fin;
    }
}
