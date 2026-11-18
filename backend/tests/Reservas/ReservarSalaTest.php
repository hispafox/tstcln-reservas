<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Aplicacion\PeticionReserva;
use App\Reservas\Aplicacion\ReservarSala;
use App\Reservas\Dominio\Capacidad;
use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\NotificadorReservas;
use App\Reservas\Dominio\ReglaDePenalizacion;
use App\Reservas\Dominio\RepositorioReservas;
use App\Reservas\Dominio\Reserva;
use App\Reservas\Dominio\Sala;
use App\Reservas\Dominio\SalaNoDisponible;
use App\Reservas\Dominio\SalaNoEncontrada;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

final class ReservarSalaTest extends TestCase
{
    private Sala $salaNorte;
    private RepositorioSalasEnMemoria $salas;
    private int $idSalaNorte;

    protected function setUp(): void
    {
        $this->salaNorte = new Sala('Sala Norte', capacidadMaxima: 10);
        $this->salas = new RepositorioSalasEnMemoria();
        $this->idSalaNorte = $this->salas->registrar($this->salaNorte);
    }

    public function test_reservar_una_sala_libre_devuelve_la_reserva(): void
    {
        $reservas = $this->createStub(RepositorioReservas::class);
        $reservar = new ReservarSala(
            $this->salas,
            $reservas,
            $this->createStub(NotificadorReservas::class),
            new ReglaDePenalizacion($reservas, new MockClock(new \DateTimeImmutable())),
        );

        $reserva = $reservar->ejecutar(new PeticionReserva($this->idSalaNorte, self::franja('10:00', '11:00'), 'Ana', new Capacidad(4)));

        self::assertSame($this->salaNorte, $reserva->sala());
    }

    public function test_reservar_una_sala_libre_la_guarda_y_avisa_una_vez(): void
    {
        $salas = new RepositorioSalasEnMemoria();
        $idSala = $salas->registrar(new Sala('Sala Norte', capacidadMaxima: 10));
        $reservas = new RepositorioReservasEnMemoria();
        $notificador = $this->createMock(NotificadorReservas::class);
        $notificador->expects($this->once())->method('reservaConfirmada');

        $reservar = new ReservarSala(
            $salas,
            $reservas,
            $notificador,
            new ReglaDePenalizacion($reservas, new MockClock('2027-03-01 09:00')),
        );

        $reserva = $reservar->ejecutar(new PeticionReserva($idSala, self::franja('10:00', '11:00'), 'Ana', new Capacidad(4)));

        self::assertSame([$reserva], $reservas->guardadas());
    }

    public function test_una_sala_que_no_existe_no_se_reserva_ni_se_avisa(): void
    {
        $reservas = new RepositorioReservasEnMemoria();
        $notificador = $this->createMock(NotificadorReservas::class);
        $notificador->expects($this->never())->method('reservaConfirmada');
        $reservar = new ReservarSala($this->salas, $reservas, $notificador, new ReglaDePenalizacion($reservas, new MockClock(new \DateTimeImmutable())));

        try {
            $reservar->ejecutar(new PeticionReserva(99, self::franja('10:00', '11:00'), 'Ana', new Capacidad(4)));
            self::fail('Se esperaba que la reserva se rechazara');
        } catch (SalaNoEncontrada) {
        }

        self::assertSame([], $reservas->guardadas());
    }

    public function test_una_reserva_que_choca_con_otra_no_se_guarda_ni_se_notifica(): void
    {
        $reservas = new RepositorioReservasEnMemoria();
        $reservas->guardar(new Reserva($this->salaNorte, self::franja('10:00', '11:00'), 'Ana', new Capacidad(4)));
        $notificador = $this->createMock(NotificadorReservas::class);
        $notificador->expects($this->never())->method('reservaConfirmada');
        $reservar = new ReservarSala($this->salas, $reservas, $notificador, new ReglaDePenalizacion($reservas, new MockClock(new \DateTimeImmutable())));

        try {
            $reservar->ejecutar(new PeticionReserva($this->idSalaNorte, self::franja('10:30', '11:30'), 'Luis', new Capacidad(3)));
            self::fail('Se esperaba que la segunda reserva se rechazara');
        } catch (\DomainException) {
        }

        self::assertCount(1, $reservas->guardadas());
    }

    public function test_no_permite_reservar_con_mas_asistentes_que_plazas_y_no_guarda_nada(): void
    {
        $reservas = new RepositorioReservasEnMemoria();
        $reservar = new ReservarSala($this->salas, $reservas, $this->createStub(NotificadorReservas::class), new ReglaDePenalizacion($reservas, new MockClock(new \DateTimeImmutable())));

        try {
            $reservar->ejecutar(new PeticionReserva($this->idSalaNorte, self::franja('10:00', '11:00'), 'Ana', new Capacidad(11)));
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
        $reservar = new ReservarSala($this->salas, $reservas, $this->createStub(NotificadorReservas::class), new ReglaDePenalizacion($reservas, new MockClock(new \DateTimeImmutable())));

        $this->expectException(SalaNoDisponible::class);

        $reservar->ejecutar(new PeticionReserva($this->idSalaNorte, self::franja('10:30', '11:30'), 'Luis', new Capacidad(3)));
    }

    private static function franja(string $inicio, string $fin): FranjaHoraria
    {
        return new FranjaHoraria(
            new \DateTimeImmutable("2027-03-04 $inicio"),
            new \DateTimeImmutable("2027-03-04 $fin"),
        );
    }
}
