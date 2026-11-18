<?php

declare(strict_types=1);

namespace App\Tests\Contrato;

use App\Reservas\Dominio\Capacidad;
use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\ListaDeEspera;
use App\Reservas\Dominio\ListasDeEspera;
use App\Reservas\Dominio\PeticionReserva;
use App\Reservas\Dominio\Sala;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class ContratoListasDeEspera extends KernelTestCase
{
    abstract protected function listas(): ListasDeEspera;

    abstract protected function salaGuardada(string $nombre): Sala;

    abstract protected function idDe(Sala $sala): int;

    public function test_una_sala_sin_peticiones_tiene_la_lista_vacia(): void
    {
        $listas = $this->listas();
        $sala = $this->salaGuardada('Sala Norte');

        self::assertSame([], $listas->deLaSala($sala)->porOrdenDeLlegada());
    }

    public function test_la_lista_guardada_vuelve_con_sus_peticiones_por_orden_de_llegada(): void
    {
        $listas = $this->listas();
        $sala = $this->salaGuardada('Sala Norte');
        $ana = $this->peticion($sala, 'Ana');
        $luis = $this->peticion($sala, 'Luis');

        $listas->guardar($sala, new ListaDeEspera([$ana, $luis]));

        self::assertEquals([$ana, $luis], $listas->deLaSala($sala)->porOrdenDeLlegada());
    }

    public function test_guardar_otra_vez_la_lista_de_una_sala_la_sustituye(): void
    {
        $listas = $this->listas();
        $sala = $this->salaGuardada('Sala Norte');
        $ana = $this->peticion($sala, 'Ana');
        $luis = $this->peticion($sala, 'Luis');
        $listas->guardar($sala, new ListaDeEspera([$ana, $luis]));

        $lista = $listas->deLaSala($sala);
        $lista->quitar($lista->porOrdenDeLlegada()[0]);
        $listas->guardar($sala, $lista);

        self::assertEquals([$luis], $listas->deLaSala($sala)->porOrdenDeLlegada());
    }

    public function test_las_listas_de_dos_salas_no_se_mezclan(): void
    {
        $listas = $this->listas();
        $norte = $this->salaGuardada('Sala Norte');
        $sur = $this->salaGuardada('Sala Sur');
        $ana = $this->peticion($norte, 'Ana');

        $listas->guardar($norte, new ListaDeEspera([$ana]));

        self::assertSame([], $listas->deLaSala($sur)->porOrdenDeLlegada());
        self::assertEquals([$ana], $listas->deLaSala($norte)->porOrdenDeLlegada());
    }

    private function peticion(Sala $sala, string $persona): PeticionReserva
    {
        return new PeticionReserva(
            $this->idDe($sala),
            new FranjaHoraria(new \DateTimeImmutable('2027-03-04 10:00'), new \DateTimeImmutable('2027-03-04 11:00')),
            $persona,
            new Capacidad(4),
        );
    }
}
