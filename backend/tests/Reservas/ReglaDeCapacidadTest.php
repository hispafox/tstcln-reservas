<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\FranjaHoraria;
use App\Reservas\ReglaDeCapacidad;
use App\Reservas\Sala;
use PHPUnit\Framework\TestCase;

final class ReglaDeCapacidadTest extends TestCase
{
    private ReglaDeCapacidad $regla;

    protected function setUp(): void
    {
        $this->regla = new ReglaDeCapacidad();
    }

    public function test_una_sala_de_diez_admite_cuatro_asistentes(): void
    {
        $sala = new Sala('Sala Norte', capacidadMaxima: 10);

        $admite = $this->regla->admite($sala, self::franja('10:00', '11:00'), 4);

        self::assertTrue($admite);
    }

    public function test_una_sala_de_diez_no_admite_once_asistentes(): void
    {
        $sala = new Sala('Sala Norte', capacidadMaxima: 10);

        self::assertFalse($this->regla->admite($sala, self::franja('10:00', '11:00'), 11));
    }

    public function test_una_sala_de_seis_no_admite_doce_asistentes(): void
    {
        $sala = new Sala('Sala Sur', capacidadMaxima: 6);

        self::assertFalse($this->regla->admite($sala, self::franja('10:00', '11:00'), 12));
    }

    public function test_una_sala_de_seis_no_admite_siete_asistentes(): void
    {
        $sala = new Sala('Sala Sur', capacidadMaxima: 6);

        self::assertFalse($this->regla->admite($sala, self::franja('10:00', '11:00'), 7));
    }

    public function test_una_sala_de_diez_admite_diez_asistentes(): void
    {
        $sala = new Sala('Sala Norte', capacidadMaxima: 10);

        self::assertTrue($this->regla->admite($sala, self::franja('10:00', '11:00'), 10));
    }

    public function test_cero_asistentes_no_se_admite(): void
    {
        $sala = new Sala('Sala Norte', capacidadMaxima: 10);

        self::assertFalse($this->regla->admite($sala, self::franja('10:00', '11:00'), 0));
    }

    public function test_una_sala_con_minimo_de_ocho_no_admite_cinco_asistentes(): void
    {
        $sala = new Sala('Sala Consejo', capacidadMaxima: 14, capacidadMinima: 8);

        self::assertFalse($this->regla->admite($sala, self::franja('10:00', '11:00'), 5));
    }

    public function test_una_sala_sin_minimo_admite_un_asistente(): void
    {
        $sala = new Sala('Sala Norte', capacidadMaxima: 10);

        self::assertTrue($this->regla->admite($sala, self::franja('10:00', '11:00'), 1));
    }

    public function test_una_sala_grande_admite_una_franja_de_diez_a_once(): void
    {
        $sala = new Sala('Auditorio', capacidadMaxima: 40);

        self::assertTrue($this->regla->admite($sala, self::franja('10:00', '11:00'), 25));
    }

    public function test_una_sala_grande_no_admite_una_franja_de_cuarenta_y_cinco_minutos(): void
    {
        $sala = new Sala('Auditorio', capacidadMaxima: 40);

        self::assertFalse($this->regla->admite($sala, self::franja('10:00', '10:45'), 25));
    }

    public function test_una_sala_grande_no_admite_una_franja_que_empieza_a_y_cuarto(): void
    {
        $sala = new Sala('Auditorio', capacidadMaxima: 40);

        self::assertFalse($this->regla->admite($sala, self::franja('10:15', '10:45'), 25));
    }

    public function test_una_sala_pequena_admite_una_franja_que_empieza_a_y_cuarto(): void
    {
        $sala = new Sala('Sala Norte', capacidadMaxima: 10);

        self::assertTrue($this->regla->admite($sala, self::franja('10:15', '11:15'), 4));
    }

    private static function franja(string $inicio, string $fin): FranjaHoraria
    {
        return new FranjaHoraria(
            new \DateTimeImmutable("2027-03-04 $inicio"),
            new \DateTimeImmutable("2027-03-04 $fin"),
        );
    }
}
