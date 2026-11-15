<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\FranjaHoraria;
use App\Reservas\HorarioLaborable;
use PHPUnit\Framework\TestCase;

final class HorarioLaborableTest extends TestCase
{
    public function test_admite_una_franja_de_un_jueves_por_la_manana(): void
    {
        $horario = new HorarioLaborable();

        $admitida = $horario->admite(self::franja('2027-03-04 10:00', '2027-03-04 11:00'));

        self::assertTrue($admitida);
    }

    public function test_no_admite_una_franja_que_empieza_antes_de_las_ocho(): void
    {
        $horario = new HorarioLaborable();

        $admitida = $horario->admite(self::franja('2027-03-04 07:30', '2027-03-04 08:30'));

        self::assertFalse($admitida);
    }

    public function test_no_admite_una_franja_que_acaba_despues_de_las_ocho_de_la_tarde(): void
    {
        $horario = new HorarioLaborable();

        $admitida = $horario->admite(self::franja('2027-03-04 19:30', '2027-03-04 20:30'));

        self::assertFalse($admitida);
    }

    public function test_no_admite_una_franja_en_sabado(): void
    {
        $horario = new HorarioLaborable();

        $admitida = $horario->admite(self::franja('2027-03-06 10:00', '2027-03-06 11:00'));

        self::assertFalse($admitida);
    }

    private static function franja(string $inicio, string $fin): FranjaHoraria
    {
        return new FranjaHoraria(new \DateTimeImmutable($inicio), new \DateTimeImmutable($fin));
    }
}
