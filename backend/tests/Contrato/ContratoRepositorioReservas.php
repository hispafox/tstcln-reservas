<?php

declare(strict_types=1);

namespace App\Tests\Contrato;

use App\Reservas\Dominio\Capacidad;
use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\RepositorioReservas;
use App\Reservas\Dominio\Reserva;
use App\Reservas\Dominio\Sala;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class ContratoRepositorioReservas extends KernelTestCase
{
    abstract protected function repositorio(): RepositorioReservas;

    abstract protected function salaGuardada(string $nombre, int $capacidadMaxima): Sala;

    abstract protected function idDe(Reserva $reserva): int;

    public function test_las_confirmadas_de_una_sala_no_incluyen_las_canceladas(): void
    {
        $repositorio = $this->repositorio();
        $sala = $this->salaGuardada('Sala Norte', 10);
        $confirmada = self::reserva($sala, 'Ana', '2027-03-04 10:00');
        $cancelada = self::reserva($sala, 'Luis', '2027-03-04 12:00');
        $cancelada->cancelarATiempo();
        $repositorio->guardar($confirmada);
        $repositorio->guardar($cancelada);

        self::assertSame([$confirmada], $repositorio->confirmadasDeLaSala($sala));
    }

    public function test_las_tardias_de_una_persona_llegan_de_la_mas_reciente_a_la_mas_antigua(): void
    {
        $repositorio = $this->repositorio();
        $sala = $this->salaGuardada('Auditorio', 40);
        $antigua = self::tardia($sala, 'Ana', '2027-03-01 10:00');
        $reciente = self::tardia($sala, 'Ana', '2027-03-10 10:00');
        $repositorio->guardar($antigua);
        $repositorio->guardar($reciente);

        self::assertSame([$reciente, $antigua], $repositorio->tardiasDeLaPersonaDesde('Ana', new \DateTimeImmutable('2027-02-01')));
    }

    public function test_las_tardias_anteriores_a_la_fecha_no_cuentan(): void
    {
        $repositorio = $this->repositorio();
        $sala = $this->salaGuardada('Auditorio', 40);
        $vieja = self::tardia($sala, 'Ana', '2027-01-10 10:00');
        $nueva = self::tardia($sala, 'Ana', '2027-03-10 10:00');
        $repositorio->guardar($vieja);
        $repositorio->guardar($nueva);

        self::assertSame([$nueva], $repositorio->tardiasDeLaPersonaDesde('Ana', new \DateTimeImmutable('2027-02-01')));
    }

    public function test_con_id_devuelve_la_reserva_guardada(): void
    {
        $repositorio = $this->repositorio();
        $sala = $this->salaGuardada('Sala Norte', 10);
        $reserva = self::reserva($sala, 'Ana', '2027-03-04 10:00');
        $repositorio->guardar($reserva);

        self::assertSame($reserva, $repositorio->conId($this->idDe($reserva)));
    }

    public function test_guardar_dos_veces_la_misma_reserva_no_la_duplica(): void
    {
        $repositorio = $this->repositorio();
        $sala = $this->salaGuardada('Auditorio', 40);
        $tardia = self::tardia($sala, 'Ana', '2027-03-10 10:00');
        $repositorio->guardar($tardia);
        $repositorio->guardar($tardia);

        self::assertSame([$tardia], $repositorio->tardiasDeLaPersonaDesde('Ana', new \DateTimeImmutable('2027-02-01')));
    }

    private static function reserva(Sala $sala, string $persona, string $inicio): Reserva
    {
        $comienzo = new \DateTimeImmutable($inicio);

        return new Reserva($sala, new FranjaHoraria($comienzo, $comienzo->modify('+1 hour')), $persona, new Capacidad(4));
    }

    private static function tardia(Sala $sala, string $persona, string $inicio): Reserva
    {
        $reserva = self::reserva($sala, $persona, $inicio);
        $reserva->cancelarTarde();

        return $reserva;
    }
}
