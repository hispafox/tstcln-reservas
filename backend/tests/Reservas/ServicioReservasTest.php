<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\FranjaHoraria;
use App\Reservas\NotificadorReservas;
use App\Reservas\RepositorioReservas;
use App\Reservas\Reserva;
use App\Reservas\Sala;
use App\Reservas\ServicioReservas;
use PHPUnit\Framework\TestCase;

final class ServicioReservasTest extends TestCase
{
    private Sala $salaNorte;

    protected function setUp(): void
    {
        $this->salaNorte = new Sala('Sala Norte', capacidadMaxima: 10);
    }

    public function test_reservar_una_sala_libre_devuelve_la_reserva(): void
    {
        $servicio = new ServicioReservas(
            $this->createStub(RepositorioReservas::class),
            $this->createStub(NotificadorReservas::class),
        );

        $reserva = $servicio->reservar($this->salaNorte, self::franja('10:00', '11:00'), 'Ana', 4);

        self::assertSame($this->salaNorte, $reserva->sala());
    }

    public function test_no_permite_reservar_una_sala_ya_ocupada(): void
    {
        $reservaDeLasDiez = new Reserva($this->salaNorte, self::franja('10:00', '11:00'), 'Ana', 4);
        $reservas = $this->createStub(RepositorioReservas::class);
        $reservas->method('confirmadasDeLaSala')->willReturn([$reservaDeLasDiez]);
        $servicio = new ServicioReservas($reservas, $this->createStub(NotificadorReservas::class));

        $this->expectException(\DomainException::class);

        $servicio->reservar($this->salaNorte, self::franja('10:30', '11:30'), 'Luis', 3);
    }

    private static function franja(string $inicio, string $fin): FranjaHoraria
    {
        return new FranjaHoraria(
            new \DateTimeImmutable("2027-03-04 $inicio"),
            new \DateTimeImmutable("2027-03-04 $fin"),
        );
    }
}
