<?php

declare(strict_types=1);

namespace App\Tests\Integracion;

use App\Reservas\Dominio\RepositorioReservas;
use App\Reservas\Dominio\Sala;
use App\Tests\Contrato\ContratoRepositorioReservas;
use App\Tests\FabricaDeDatos;
use Doctrine\ORM\EntityManagerInterface;

final class RepositorioReservasDoctrineTest extends ContratoRepositorioReservas
{
    protected function repositorio(): RepositorioReservas
    {
        return static::getContainer()->get(RepositorioReservas::class);
    }

    protected function salaGuardada(string $nombre, int $capacidadMaxima): Sala
    {
        $sala = new Sala($nombre, capacidadMaxima: $capacidadMaxima);
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $em->persist($sala);
        $em->flush();

        return $sala;
    }

    public function test_las_reservas_de_otra_sala_no_cuentan_como_confirmadas_de_esta(): void
    {
        self::bootKernel();
        $datos = new FabricaDeDatos(static::getContainer()->get(EntityManagerInterface::class));
        $repositorio = static::getContainer()->get(RepositorioReservas::class);
        $salaNorte = $datos->sala();
        $salaSur = $datos->sala('Sala Sur', capacidadMaxima: 6);
        $deLaNorte = $datos->reserva($salaNorte, '2027-03-04 10:00', '2027-03-04 11:00');
        $datos->reserva($salaSur, '2027-03-04 10:00', '2027-03-04 11:00');

        $resultado = $repositorio->confirmadasDeLaSala($salaNorte);

        self::assertSame([$deLaNorte], $resultado);
    }
}
