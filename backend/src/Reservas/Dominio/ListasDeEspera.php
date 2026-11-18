<?php

declare(strict_types=1);

namespace App\Reservas\Dominio;

interface ListasDeEspera
{
    public function deLaSala(Sala $sala): ListaDeEspera;

    public function guardar(Sala $sala, ListaDeEspera $lista): void;
}
