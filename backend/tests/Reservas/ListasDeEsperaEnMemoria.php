<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Dominio\ListaDeEspera;
use App\Reservas\Dominio\ListasDeEspera;
use App\Reservas\Dominio\PeticionReserva;
use App\Reservas\Dominio\Sala;

final class ListasDeEsperaEnMemoria implements ListasDeEspera
{
    /** @var array<int, list<PeticionReserva>> */
    private array $peticiones = [];

    public function deLaSala(Sala $sala): ListaDeEspera
    {
        return new ListaDeEspera($this->peticiones[spl_object_id($sala)] ?? []);
    }

    public function guardar(Sala $sala, ListaDeEspera $lista): void
    {
        $this->peticiones[spl_object_id($sala)] = $lista->porOrdenDeLlegada();
    }
}
