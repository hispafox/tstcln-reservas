<?php

declare(strict_types=1);

namespace App\Tests\Integracion;

use App\Reservas\Dominio\RepositorioReservas;
use App\Tests\FabricaDeDatos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class RepositorioReservasDoctrineTest extends KernelTestCase
{
    public function test_las_reservas_canceladas_no_cuentan_como_confirmadas(): void
    {
        self::bootKernel();
        $datos = new FabricaDeDatos(static::getContainer()->get(EntityManagerInterface::class));
        $repositorio = static::getContainer()->get(RepositorioReservas::class);
        $sala = $datos->sala();
        $confirmada = $datos->reserva($sala, '2027-03-04 10:00', '2027-03-04 11:00');
        $cancelada = $datos->reserva($sala, '2027-03-04 12:00', '2027-03-04 13:00');
        $cancelada->cancelarATiempo();
        $repositorio->guardar($cancelada);

        $resultado = $repositorio->confirmadasDeLaSala($sala);

        self::assertSame([$confirmada], $resultado);
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
