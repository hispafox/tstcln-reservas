<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Dominio\Capacidad;
use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\ReglaDeCapacidad;
use App\Reservas\Dominio\Sala;
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

        $admite = $this->regla->admite($sala, self::franja('10:00', '11:00'), new Capacidad(4));

        self::assertTrue($admite);
    }

    public function test_una_sala_de_diez_no_admite_once_asistentes(): void
    {
        $sala = new Sala('Sala Norte', capacidadMaxima: 10);

        self::assertFalse($this->regla->admite($sala, self::franja('10:00', '11:00'), new Capacidad(11)));
    }

    public function test_una_sala_de_seis_no_admite_doce_asistentes(): void
    {
        $sala = new Sala('Sala Sur', capacidadMaxima: 6);

        self::assertFalse($this->regla->admite($sala, self::franja('10:00', '11:00'), new Capacidad(12)));
    }

    public function test_una_sala_de_seis_no_admite_siete_asistentes(): void
    {
        $sala = new Sala('Sala Sur', capacidadMaxima: 6);

        self::assertFalse($this->regla->admite($sala, self::franja('10:00', '11:00'), new Capacidad(7)));
    }

    public function test_una_sala_de_diez_admite_diez_asistentes(): void
    {
        $sala = new Sala('Sala Norte', capacidadMaxima: 10);

        self::assertTrue($this->regla->admite($sala, self::franja('10:00', '11:00'), new Capacidad(10)));
    }

    public function test_una_sala_con_minimo_de_ocho_no_admite_cinco_asistentes(): void
    {
        $sala = new Sala('Sala Consejo', capacidadMaxima: 14, capacidadMinima: 8);

        self::assertFalse($this->regla->admite($sala, self::franja('10:00', '11:00'), new Capacidad(5)));
    }

    public function test_una_sala_sin_minimo_admite_un_asistente(): void
    {
        $sala = new Sala('Sala Norte', capacidadMaxima: 10);

        self::assertTrue($this->regla->admite($sala, self::franja('10:00', '11:00'), new Capacidad(1)));
    }

    public function test_una_sala_grande_admite_una_franja_de_diez_a_once(): void
    {
        $sala = new Sala('Auditorio', capacidadMaxima: 40);

        self::assertTrue($this->regla->admite($sala, self::franja('10:00', '11:00'), new Capacidad(25)));
    }

    public function test_una_sala_grande_admite_una_franja_de_diez_y_media_a_once_y_media(): void
    {
        $sala = new Sala('Auditorio', capacidadMaxima: 40);

        self::assertTrue($this->regla->admite($sala, self::franja('10:30', '11:30'), new Capacidad(25)));
    }

    public function test_una_sala_grande_no_admite_una_franja_de_cuarenta_y_cinco_minutos(): void
    {
        $sala = new Sala('Auditorio', capacidadMaxima: 40);

        self::assertFalse($this->regla->admite($sala, self::franja('10:00', '10:45'), new Capacidad(25)));
    }

    public function test_una_sala_grande_no_admite_una_franja_que_empieza_a_y_cuarto(): void
    {
        $sala = new Sala('Auditorio', capacidadMaxima: 40);

        self::assertFalse($this->regla->admite($sala, self::franja('10:15', '10:45'), new Capacidad(25)));
    }

    public function test_una_sala_pequena_admite_una_franja_que_empieza_a_y_cuarto(): void
    {
        $sala = new Sala('Sala Norte', capacidadMaxima: 10);

        self::assertTrue($this->regla->admite($sala, self::franja('10:15', '11:15'), new Capacidad(4)));
    }

    private static function franja(string $inicio, string $fin): FranjaHoraria
    {
        return new FranjaHoraria(
            new \DateTimeImmutable("2027-03-04 $inicio"),
            new \DateTimeImmutable("2027-03-04 $fin"),
        );
    }
}
