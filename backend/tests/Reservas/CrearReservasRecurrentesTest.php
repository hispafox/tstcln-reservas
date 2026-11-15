<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\CrearReservasRecurrentes;
use App\Reservas\FranjaHoraria;
use App\Reservas\NotificadorReservas;
use App\Reservas\Reserva;
use App\Reservas\Sala;
use PHPUnit\Framework\TestCase;

final class CrearReservasRecurrentesTest extends TestCase
{
    private Sala $salaNorte;

    protected function setUp(): void
    {
        $this->salaNorte = new Sala('Sala Norte', capacidadMaxima: 10);
    }

    public function test_crea_las_semanas_libres_y_avisa_una_vez_con_la_serie(): void
    {
        $reservas = new RepositorioReservasEnMemoria();
        $reservas->guardar(new Reserva($this->salaNorte, self::franja('2027-03-18 10:30', '2027-03-18 11:30'), 'Luis', 3));
        $notificador = $this->createMock(NotificadorReservas::class);
        $notificador->expects($this->once())
            ->method('serieConfirmada')
            ->with($this->callback(fn (array $creadas) => count($creadas) === 3));
        $crear = new CrearReservasRecurrentes($reservas, $notificador);

        $resultado = $crear->ejecutar(
            $this->salaNorte,
            self::franja('2027-03-04 10:00', '2027-03-04 11:00'),
            new \DateTimeImmutable('2027-03-31'),
            'Ana',
            4,
        );

        self::assertCount(3, $resultado->libres());
        self::assertCount(1, $resultado->descartadas());
        self::assertCount(4, $reservas->guardadas());
    }

    public function test_una_serie_sin_ninguna_semana_libre_no_crea_nada_ni_avisa(): void
    {
        $reservas = new RepositorioReservasEnMemoria();
        $reservas->guardar(new Reserva($this->salaNorte, self::franja('2027-03-04 10:00', '2027-03-04 11:00'), 'Luis', 3));
        $notificador = $this->createMock(NotificadorReservas::class);
        $notificador->expects($this->never())->method('serieConfirmada');
        $crear = new CrearReservasRecurrentes($reservas, $notificador);

        try {
            $crear->ejecutar($this->salaNorte, self::franja('2027-03-04 10:00', '2027-03-04 11:00'), new \DateTimeImmutable('2027-03-04'), 'Ana', 4);
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
        $crear = new CrearReservasRecurrentes($reservas, $notificador);

        try {
            $crear->ejecutar($this->salaNorte, self::franja('2027-03-04 10:00', '2027-03-04 11:00'), new \DateTimeImmutable('2027-03-31'), 'Ana', 11);
            self::fail('Se esperaba que la serie se rechazara');
        } catch (\DomainException) {
        }

        self::assertSame([], $reservas->guardadas());
    }

    private static function franja(string $inicio, string $fin): FranjaHoraria
    {
        return new FranjaHoraria(new \DateTimeImmutable($inicio), new \DateTimeImmutable($fin));
    }
}
