<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\FranjaHoraria;
use App\Reservas\Reserva;
use App\Reservas\Sala;
use App\Reservas\ServicioCancelacion;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ServicioCancelacionTest extends TestCase
{
    private Sala $salaNorte;

    protected function setUp(): void
    {
        $this->salaNorte = new Sala('Sala Norte', capacidadMaxima: 10);
    }

    #[DataProvider('momentosDeCancelacion')]
    public function test_una_cancelacion_con_menos_de_dos_horas_de_antelacion_es_tardia(
        string $momento,
        string $estadoEsperado,
    ): void {
        $reserva = new Reserva($this->salaNorte, self::franja('10:00', '11:00'), 'Ana', 4);
        $servicio = new ServicioCancelacion(new RepositorioReservasEnMemoria());

        $servicio->cancelar($reserva, new \DateTimeImmutable($momento));

        self::assertSame($estadoEsperado, $reserva->estado());
    }

    public static function momentosDeCancelacion(): array
    {
        return [
            'la tarde anterior' => ['2027-03-03 18:00', 'cancelada_a_tiempo'],
            'con dos horas justas' => ['2027-03-04 08:00', 'cancelada_a_tiempo'],
            'con una hora y cincuenta y nueve minutos' => ['2027-03-04 08:01', 'cancelada_tarde'],
            'media hora antes' => ['2027-03-04 09:30', 'cancelada_tarde'],
            'justo a la hora de empezar' => ['2027-03-04 10:00', 'cancelada_tarde'],
        ];
    }

    private static function franja(string $inicio, string $fin): FranjaHoraria
    {
        return new FranjaHoraria(
            new \DateTimeImmutable("2027-03-04 $inicio"),
            new \DateTimeImmutable("2027-03-04 $fin"),
        );
    }
}
