<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Capacidad;
use App\Reservas\FranjaHoraria;
use App\Reservas\ReglaDePenalizacion;
use App\Reservas\Reserva;
use App\Reservas\Sala;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

final class ReglaDePenalizacionTest extends TestCase
{
    private RepositorioReservasEnMemoria $reservas;
    private Sala $salaGrande;

    protected function setUp(): void
    {
        $this->reservas = new RepositorioReservasEnMemoria();
        $this->salaGrande = new Sala('Sala Grande', capacidadMaxima: 20);
    }

    public function test_tres_tardias_de_esta_semana_penalizan_en_una_sala_grande(): void
    {
        $this->tardias('2027-03-09 10:00', '2027-03-08 10:00', '2027-03-07 10:00');

        self::assertTrue($this->regla('2027-03-10 12:00')->penaliza('Ana', $this->salaGrande));
    }

    public function test_en_una_sala_pequena_no_penaliza(): void
    {
        $this->tardias('2027-03-09 10:00', '2027-03-08 10:00', '2027-03-07 10:00');

        self::assertFalse($this->regla('2027-03-10 12:00')->penaliza('Ana', new Sala('Sala Sur', capacidadMaxima: 6)));
    }

    public function test_dos_tardias_no_penalizan(): void
    {
        $this->tardias('2027-03-09 10:00', '2027-03-08 10:00');

        self::assertFalse($this->regla('2027-03-10 12:00')->penaliza('Ana', $this->salaGrande));
    }

    public function test_la_semana_se_cuenta_desde_la_tercera_tardia_mas_reciente(): void
    {
        $this->tardias('2027-03-09 10:00', '2027-02-17 10:00', '2027-02-16 10:00');

        self::assertFalse($this->regla('2027-03-10 12:00')->penaliza('Ana', $this->salaGrande));
    }

    public function test_a_los_siete_dias_justos_de_la_tercera_tardia_ya_no_penaliza(): void
    {
        $this->tardias('2027-03-05 12:00', '2027-03-04 12:00', '2027-03-03 12:00');

        self::assertFalse($this->regla('2027-03-10 12:00')->penaliza('Ana', $this->salaGrande));
    }

    public function test_un_minuto_antes_de_los_siete_dias_todavia_penaliza(): void
    {
        $this->tardias('2027-03-05 12:00', '2027-03-04 12:00', '2027-03-03 12:00');

        self::assertTrue($this->regla('2027-03-10 11:59')->penaliza('Ana', $this->salaGrande));
    }

    private function regla(string $ahora): ReglaDePenalizacion
    {
        return new ReglaDePenalizacion($this->reservas, new MockClock(new \DateTimeImmutable($ahora)));
    }

    private function tardias(string ...$inicios): void
    {
        foreach ($inicios as $inicio) {
            $reserva = new Reserva(
                $this->salaGrande,
                new FranjaHoraria(new \DateTimeImmutable($inicio), new \DateTimeImmutable($inicio.' +1 hour')),
                'Ana',
                new Capacidad(4),
            );
            $reserva->cancelarTarde();
            $this->reservas->guardar($reserva);
        }
    }
}
