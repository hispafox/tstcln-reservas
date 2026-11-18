<?php

declare(strict_types=1);

namespace App\Tests\Integracion;

use App\Reservas\Dominio\ListasDeEspera;
use App\Reservas\Dominio\Sala;
use App\Tests\Contrato\ContratoListasDeEspera;
use Doctrine\ORM\EntityManagerInterface;

final class ListasDeEsperaDoctrineTest extends ContratoListasDeEspera
{
    protected function listas(): ListasDeEspera
    {
        $listas = static::getContainer()->get(ListasDeEspera::class);
        self::assertInstanceOf(ListasDeEspera::class, $listas);

        return $listas;
    }

    protected function salaGuardada(string $nombre): Sala
    {
        $sala = new Sala($nombre, capacidadMaxima: 10);
        $em = static::getContainer()->get(EntityManagerInterface::class);
        self::assertInstanceOf(EntityManagerInterface::class, $em);
        $em->persist($sala);
        $em->flush();

        return $sala;
    }

    protected function idDe(Sala $sala): int
    {
        return $sala->id() ?? throw new \LogicException('La sala no está guardada');
    }
}
