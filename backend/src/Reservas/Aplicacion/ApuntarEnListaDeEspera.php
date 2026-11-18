<?php

declare(strict_types=1);

namespace App\Reservas\Aplicacion;

use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\FranjaLibre;
use App\Reservas\Dominio\ListasDeEspera;
use App\Reservas\Dominio\PeticionReserva;
use App\Reservas\Dominio\RepositorioReservas;
use App\Reservas\Dominio\RepositorioSalas;
use App\Reservas\Dominio\Sala;
use App\Reservas\Dominio\SalaNoEncontrada;

final class ApuntarEnListaDeEspera
{
    public function __construct(
        private RepositorioSalas $salas,
        private RepositorioReservas $reservas,
        private ListasDeEspera $listas,
    ) {}

    public function ejecutar(PeticionReserva $peticion): int
    {
        $sala = $this->salas->conId($peticion->sala) ?? throw new SalaNoEncontrada($peticion->sala);

        if (!$this->estaOcupada($sala, $peticion->franja)) {
            throw new FranjaLibre($sala, $peticion->franja);
        }

        $lista = $this->listas->deLaSala($sala);
        $posicion = $lista->apuntar($peticion);
        $this->listas->guardar($sala, $lista);

        return $posicion;
    }

    private function estaOcupada(Sala $sala, FranjaHoraria $franja): bool
    {
        foreach ($this->reservas->confirmadasDeLaSala($sala) as $reserva) {
            if ($reserva->franja()->seSolapaCon($franja)) {
                return true;
            }
        }

        return false;
    }
}
