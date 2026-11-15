<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\FranjaHoraria;
use PHPUnit\Framework\TestCase;

final class FranjaHorariaTest extends TestCase
{
    public function test_dos_franjas_que_comparten_media_hora_se_solapan(): void
    {
        $deDiezAOnce = self::franja('10:00', '11:00');

        $seSolapan = $deDiezAOnce->seSolapaCon(self::franja('10:30', '11:30'));

        self::assertTrue($seSolapan);
    }

    public function test_una_franja_que_empieza_cuando_acaba_otra_no_se_solapa_con_ella(): void
    {
        $deDiezAOnce = self::franja('10:00', '11:00');

        $seSolapan = $deDiezAOnce->seSolapaCon(self::franja('11:00', '12:00'));

        self::assertFalse($seSolapan);
    }

    public function test_una_franja_no_puede_terminar_antes_de_empezar(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        self::franja('11:00', '10:00');
    }

    public function test_una_franja_no_puede_durar_cero_minutos(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        self::franja('10:00', '10:00');
    }

    private static function franja(string $inicio, string $fin): FranjaHoraria
    {
        return new FranjaHoraria(
            new \DateTimeImmutable("2027-03-04 $inicio"),
            new \DateTimeImmutable("2027-03-04 $fin"),
        );
    }
}
