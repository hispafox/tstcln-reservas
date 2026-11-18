<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Dominio\ListasDeEspera;
use App\Reservas\Dominio\Sala;
use App\Tests\Contrato\ContratoListasDeEspera;

final class ListasDeEsperaEnMemoriaTest extends ContratoListasDeEspera
{
    private ListasDeEsperaEnMemoria $listas;
    private RepositorioSalasEnMemoria $salas;

    protected function setUp(): void
    {
        $this->listas = new ListasDeEsperaEnMemoria();
        $this->salas = new RepositorioSalasEnMemoria();
    }

    protected function listas(): ListasDeEspera
    {
        return $this->listas;
    }

    protected function salaGuardada(string $nombre): Sala
    {
        $sala = new Sala($nombre, capacidadMaxima: 10);
        $this->salas->registrar($sala);

        return $sala;
    }

    protected function idDe(Sala $sala): int
    {
        return 1;
    }
}
