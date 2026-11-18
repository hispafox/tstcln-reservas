<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Aplicacion\CrearReservasRecurrentes;
use App\Reservas\Aplicacion\PeticionReserva;
use App\Reservas\Dominio\Capacidad;
use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\NotificadorReservas;
use App\Reservas\Dominio\ReglaDePenalizacion;
use App\Reservas\Dominio\Reserva;
use App\Reservas\Dominio\Sala;
use App\Reservas\Dominio\SalaNoEncontrada;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

final class CrearReservasRecurrentesTest extends TestCase
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

    public function test_crea_las_semanas_libres_y_avisa_una_vez_con_la_serie(): void
    {
        $reservas = new RepositorioReservasEnMemoria();
        $reservas->guardar(new Reserva($this->salaNorte, self::franja('2027-03-18 10:30', '2027-03-18 11:30'), 'Luis', new Capacidad(3)));
        $notificador = $this->createMock(NotificadorReservas::class);
        $notificador->expects($this->once())
            ->method('serieConfirmada')
            ->with($this->callback(fn(array $creadas) => count($creadas) === 3));
        $crear = new CrearReservasRecurrentes($this->salas, $reservas, $notificador, new ReglaDePenalizacion($reservas, new MockClock(new \DateTimeImmutable('2027-03-01 09:00'))));

        $resultado = $crear->ejecutar(
            new PeticionReserva($this->idSalaNorte, self::franja('2027-03-04 10:00', '2027-03-04 11:00'), 'Ana', new Capacidad(4)),
            new \DateTimeImmutable('2027-03-31'),
        );

        self::assertCount(3, $resultado->libres());
        self::assertCount(1, $resultado->descartadas());
        self::assertCount(4, $reservas->guardadas());
    }

    public function test_una_serie_sin_ninguna_semana_libre_no_crea_nada_ni_avisa(): void
    {
        $reservas = new RepositorioReservasEnMemoria();
        $reservas->guardar(new Reserva($this->salaNorte, self::franja('2027-03-04 10:00', '2027-03-04 11:00'), 'Luis', new Capacidad(3)));
        $notificador = $this->createMock(NotificadorReservas::class);
        $notificador->expects($this->never())->method('serieConfirmada');
        $crear = new CrearReservasRecurrentes($this->salas, $reservas, $notificador, new ReglaDePenalizacion($reservas, new MockClock(new \DateTimeImmutable('2027-03-01 09:00'))));

        try {
            $crear->ejecutar(
                new PeticionReserva($this->idSalaNorte, self::franja('2027-03-04 10:00', '2027-03-04 11:00'), 'Ana', new Capacidad(4)),
                new \DateTimeImmutable('2027-03-04'),
            );
            self::fail('Se esperaba que la serie se rechazara');
        } catch (\DomainException) {
        }

        self::assertCount(1, $reservas->guardadas());
    }

    public function test_una_serie_con_mas_asistentes_que_plazas_no_crea_nada_ni_avisa(): void
    {
        $reservas = new RepositorioReservasEnMemoria();
        $notificador = $this->createMock(NotificadorReservas::class);
        $notificador->expects($this->never())->method('serieConfirmada');
        $crear = new CrearReservasRecurrentes($this->salas, $reservas, $notificador, new ReglaDePenalizacion($reservas, new MockClock(new \DateTimeImmutable('2027-03-01 09:00'))));

        try {
            $crear->ejecutar(
                new PeticionReserva($this->idSalaNorte, self::franja('2027-03-04 10:00', '2027-03-04 11:00'), 'Ana', new Capacidad(11)),
                new \DateTimeImmutable('2027-03-31'),
            );
            self::fail('Se esperaba que la serie se rechazara');
        } catch (\DomainException) {
        }

        self::assertSame([], $reservas->guardadas());
    }

    public function test_una_serie_para_una_sala_que_no_existe_no_crea_nada_ni_avisa(): void
    {
        $reservas = new RepositorioReservasEnMemoria();
        $notificador = $this->createMock(NotificadorReservas::class);
        $notificador->expects($this->never())->method('serieConfirmada');
        $crear = new CrearReservasRecurrentes($this->salas, $reservas, $notificador, new ReglaDePenalizacion($reservas, new MockClock(new \DateTimeImmutable('2027-03-01 09:00'))));

        try {
            $crear->ejecutar(
                new PeticionReserva(99, self::franja('2027-03-04 10:00', '2027-03-04 11:00'), 'Ana', new Capacidad(4)),
                new \DateTimeImmutable('2027-03-31'),
            );
            self::fail('Se esperaba que la serie se rechazara');
        } catch (SalaNoEncontrada) {
        }

        self::assertSame([], $reservas->guardadas());
    }

    private static function franja(string $inicio, string $fin): FranjaHoraria
    {
        return new FranjaHoraria(new \DateTimeImmutable($inicio), new \DateTimeImmutable($fin));
    }
}
