<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Aplicacion\CancelarReserva;
use App\Reservas\Dominio\Capacidad;
use App\Reservas\Dominio\EstadoReserva;
use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\Reserva;
use App\Reservas\Dominio\Sala;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

final class CancelarReservaTest extends TestCase
{
    public function test_una_reserva_cancelada_con_mucha_antelacion_queda_cancelada_a_tiempo(): void
    {
        $reservas = new RepositorioReservasEnMemoria();
        $reserva = new Reserva(
            new Sala('Sala Norte', capacidadMaxima: 10),
            new FranjaHoraria(new \DateTimeImmutable('2027-03-04 10:00'), new \DateTimeImmutable('2027-03-04 11:00')),
            'Ana',
            new Capacidad(4),
        );
        $reservas->guardar($reserva);
        $cancelar = new CancelarReserva($reservas, new MockClock(new \DateTimeImmutable('2027-03-01 09:00')));

        $cancelar->ejecutar($reservas->idDe($reserva));

        self::assertSame(EstadoReserva::CanceladaATiempo, $reserva->estado());
    }
}
