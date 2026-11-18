<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Aplicacion\PeticionReserva;
use App\Reservas\Aplicacion\ServicioReservas;
use App\Reservas\Dominio\Capacidad;
use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\NotificadorReservas;
use App\Reservas\Dominio\ReglaDePenalizacion;
use App\Reservas\Dominio\RepositorioReservas;
use App\Reservas\Dominio\Reserva;
use App\Reservas\Dominio\Sala;
use App\Reservas\Dominio\SalaNoDisponible;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

final class ServicioReservasTest extends TestCase
{
    private Sala $salaNorte;

    protected function setUp(): void
    {
        $this->salaNorte = new Sala('Sala Norte', capacidadMaxima: 10);
    }

    public function test_reservar_una_sala_libre_devuelve_la_reserva(): void
    {
        $reservas = $this->createStub(RepositorioReservas::class);
        $servicio = new ServicioReservas(
            $reservas,
            $this->createStub(NotificadorReservas::class),
            new ReglaDePenalizacion($reservas, new MockClock(new \DateTimeImmutable())),
        );

        $reserva = $servicio->reservar(new PeticionReserva($this->salaNorte, self::franja('10:00', '11:00'), 'Ana', new Capacidad(4)));

        self::assertSame($this->salaNorte, $reserva->sala());
    }

    public function test_una_reserva_valida_queda_guardada_y_se_notifica_una_vez(): void
    {
        $reservas = new RepositorioReservasEnMemoria();
        $notificador = $this->createMock(NotificadorReservas::class);
        $notificador->expects($this->once())
            ->method('reservaConfirmada')
            ->with($this->callback(fn (Reserva $reserva) => $reserva->sala() === $this->salaNorte));
        $servicio = new ServicioReservas($reservas, $notificador, new ReglaDePenalizacion($reservas, new MockClock(new \DateTimeImmutable())));

        $reserva = $servicio->reservar(new PeticionReserva($this->salaNorte, self::franja('10:00', '11:00'), 'Ana', new Capacidad(4)));

        self::assertSame([$reserva], $reservas->guardadas());
    }

    public function test_una_reserva_que_choca_con_otra_no_se_guarda_ni_se_notifica(): void
    {
        $reservas = new RepositorioReservasEnMemoria();
        $reservas->guardar(new Reserva($this->salaNorte, self::franja('10:00', '11:00'), 'Ana', new Capacidad(4)));
        $notificador = $this->createMock(NotificadorReservas::class);
        $notificador->expects($this->never())->method('reservaConfirmada');
        $servicio = new ServicioReservas($reservas, $notificador, new ReglaDePenalizacion($reservas, new MockClock(new \DateTimeImmutable())));

        try {
            $servicio->reservar(new PeticionReserva($this->salaNorte, self::franja('10:30', '11:30'), 'Luis', new Capacidad(3)));
            self::fail('Se esperaba que la segunda reserva se rechazara');
        } catch (\DomainException) {
        }

        self::assertCount(1, $reservas->guardadas());
    }

    public function test_no_permite_reservar_con_mas_asistentes_que_plazas_y_no_guarda_nada(): void
    {
        $reservas = new RepositorioReservasEnMemoria();
        $servicio = new ServicioReservas($reservas, $this->createStub(NotificadorReservas::class), new ReglaDePenalizacion($reservas, new MockClock(new \DateTimeImmutable())));

        try {
            $servicio->reservar(new PeticionReserva($this->salaNorte, self::franja('10:00', '11:00'), 'Ana', new Capacidad(11)));
            self::fail('Se esperaba que la reserva se rechazara');
        } catch (\DomainException) {
        }

        self::assertSame([], $reservas->guardadas());
    }

    public function test_no_permite_reservar_una_sala_ya_ocupada(): void
    {
        $reservaDeLasDiez = new Reserva($this->salaNorte, self::franja('10:00', '11:00'), 'Ana', new Capacidad(4));
        $reservas = $this->createStub(RepositorioReservas::class);
        $reservas->method('confirmadasDeLaSala')->willReturn([$reservaDeLasDiez]);
        $servicio = new ServicioReservas($reservas, $this->createStub(NotificadorReservas::class), new ReglaDePenalizacion($reservas, new MockClock(new \DateTimeImmutable())));

        $this->expectException(SalaNoDisponible::class);

        $servicio->reservar(new PeticionReserva($this->salaNorte, self::franja('10:30', '11:30'), 'Luis', new Capacidad(3)));
    }

    private static function franja(string $inicio, string $fin): FranjaHoraria
    {
        return new FranjaHoraria(
            new \DateTimeImmutable("2027-03-04 $inicio"),
            new \DateTimeImmutable("2027-03-04 $fin"),
        );
    }
}
