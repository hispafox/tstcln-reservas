<?php

declare(strict_types=1);

namespace App\Salas\Aplicacion;

use App\Reservas\FranjaHoraria;
use App\Salas\Dominio\AgendaReservas;
use App\Salas\Dominio\CalculadoraOcupacion;
use App\Salas\Dominio\CatalogoSalas;
use App\Salas\Dominio\OcupacionSala;

final class CalcularOcupacion
{
    public function __construct(
        private CatalogoSalas $catalogo,
        private AgendaReservas $agenda,
        private CalculadoraOcupacion $calculadora,
    ) {
    }

    /**
     * @return list<OcupacionSala>
     */
    public function ejecutar(FranjaHoraria $periodo): array
    {
        $ocupaciones = [];
        foreach ($this->catalogo->todas() as $sala) {
            $ocupaciones[] = new OcupacionSala(
                $sala->id(),
                $sala->nombre(),
                $this->calculadora->porcentaje($periodo, $this->agenda->franjasConfirmadas($sala, $periodo)),
            );
        }

        return $ocupaciones;
    }
}
