<?php

declare(strict_types=1);

namespace App\Reservas\Dominio;

final class SerieSemanal
{
    public function __construct(
        private FranjaHoraria $primera,
        private \DateTimeImmutable $hasta,
    ) {}

    /** @return FranjaHoraria[] */
    public function franjas(): array
    {
        $franjas = [];
        $franja = $this->primera;

        while ($franja->inicio()->format('Y-m-d') <= $this->hasta->format('Y-m-d')) {
            $franjas[] = $franja;
            $franja = new FranjaHoraria(
                $franja->inicio()->modify('+1 week'),
                $franja->fin()->modify('+1 week'),
            );
        }

        return $franjas;
    }

    /** @param FranjaHoraria[] $ocupadas */
    public function repartir(array $ocupadas): ResultadoSerie
    {
        $libres = [];
        $descartadas = [];

        foreach ($this->franjas() as $franja) {
            $choca = array_filter($ocupadas, fn(FranjaHoraria $ocupada) => $ocupada->seSolapaCon($franja));

            if ($choca === []) {
                $libres[] = $franja;
            } else {
                $descartadas[] = $franja;
            }
        }

        return new ResultadoSerie($libres, $descartadas);
    }
}
