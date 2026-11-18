<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Dominio\RepositorioReservas;
use App\Reservas\Dominio\Reserva;
use App\Reservas\Dominio\Sala;
use App\Tests\Contrato\ContratoRepositorioReservas;

final class RepositorioReservasEnMemoriaTest extends ContratoRepositorioReservas
{
    private RepositorioReservasEnMemoria $repositorio;

    protected function setUp(): void
    {
        $this->repositorio = new RepositorioReservasEnMemoria();
    }

    protected function repositorio(): RepositorioReservas
    {
        return $this->repositorio;
    }

    protected function salaGuardada(string $nombre, int $capacidadMaxima): Sala
    {
        return new Sala($nombre, capacidadMaxima: $capacidadMaxima);
    }

    protected function idDe(Reserva $reserva): int
    {
        return $this->repositorio->idDe($reserva);
    }
}
