<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\FranjaHoraria;
use App\Reservas\NotificadorReservas;
use App\Reservas\RepositorioReservas;
use App\Reservas\Sala;
use App\Reservas\ServicioReservas;
use PHPUnit\Framework\TestCase;

final class ServicioReservasTest extends TestCase
{
    public function test_comprueba_la_disponibilidad_antes_de_reservar(): void
    {
        $sala = new Sala('Sala Norte', capacidadMaxima: 10);
        $franja = new FranjaHoraria(
            new \DateTimeImmutable('2027-03-04 10:00'),
            new \DateTimeImmutable('2027-03-04 11:00'),
        );

        $servicio = $this->getMockBuilder(ServicioReservas::class)
            ->setConstructorArgs([
                $this->createStub(RepositorioReservas::class),
                $this->createStub(NotificadorReservas::class),
            ])
            ->onlyMethods(['comprobarDisponibilidad'])
            ->getMock();

        $servicio->expects($this->once())
            ->method('comprobarDisponibilidad')
            ->with($sala, $franja);

        $servicio->reservar($sala, $franja, 'Ana', 4);
    }

    public function test_no_permite_reservar_una_sala_ya_ocupada(): void
    {
        $sala = new Sala('Sala Norte', capacidadMaxima: 10);
        $servicio = new ServicioReservas(
            new RepositorioReservasEnMemoria(),
            $this->createStub(NotificadorReservas::class),
        );
        $servicio->reservar($sala, new FranjaHoraria(
            new \DateTimeImmutable('2027-03-04 10:00'),
            new \DateTimeImmutable('2027-03-04 11:00'),
        ), 'Ana', 4);

        $this->expectException(\DomainException::class);

        $servicio->reservar($sala, new FranjaHoraria(
            new \DateTimeImmutable('2027-03-04 10:30'),
            new \DateTimeImmutable('2027-03-04 11:30'),
        ), 'Luis', 3);
    }
}
