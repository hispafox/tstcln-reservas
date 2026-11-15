<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\FranjaHoraria;
use App\Reservas\LectorDeFechas;
use App\Reservas\SerieSemanal;
use PHPUnit\Framework\TestCase;

final class SerieSemanalTest extends TestCase
{
    public function test_una_serie_hasta_el_mismo_dia_tiene_una_sola_franja(): void
    {
        $serie = new SerieSemanal(self::franja('2027-03-04 10:00', '2027-03-04 11:00'), new \DateTimeImmutable('2027-03-04'));

        self::assertCount(1, $serie->franjas());
    }

    public function test_una_serie_hasta_la_vispera_de_la_segunda_semana_tiene_una_sola_franja(): void
    {
        $serie = new SerieSemanal(self::franja('2027-03-04 10:00', '2027-03-04 11:00'), new \DateTimeImmutable('2027-03-10'));

        self::assertCount(1, $serie->franjas());
    }

    public function test_una_serie_de_marzo_tiene_una_franja_cada_jueves(): void
    {
        $serie = new SerieSemanal(self::franja('2027-03-04 10:00', '2027-03-04 11:00'), new \DateTimeImmutable('2027-03-31'));

        $inicios = array_map(fn (FranjaHoraria $franja) => $franja->inicio()->format('Y-m-d H:i'), $serie->franjas());

        self::assertSame(['2027-03-04 10:00', '2027-03-11 10:00', '2027-03-18 10:00', '2027-03-25 10:00'], $inicios);
    }

    public function test_reparte_las_semanas_entre_libres_y_descartadas(): void
    {
        $serie = new SerieSemanal(self::franja('2027-03-04 10:00', '2027-03-04 11:00'), new \DateTimeImmutable('2027-03-31'));

        $resultado = $serie->repartir([self::franja('2027-03-18 10:30', '2027-03-18 11:30')]);

        self::assertSame(['2027-03-04', '2027-03-11', '2027-03-25'], self::dias($resultado->libres()));
        self::assertSame(['2027-03-18'], self::dias($resultado->descartadas()));
    }

    public function test_una_reserva_que_solo_toca_la_frontera_no_descarta_la_semana(): void
    {
        $serie = new SerieSemanal(self::franja('2027-03-04 10:00', '2027-03-04 11:00'), new \DateTimeImmutable('2027-03-04'));

        $resultado = $serie->repartir([self::franja('2027-03-04 11:00', '2027-03-04 12:00')]);

        self::assertSame(['2027-03-04'], self::dias($resultado->libres()));
        self::assertSame([], $resultado->descartadas());
    }

    public function test_una_serie_que_cruza_el_cambio_de_hora_sigue_a_las_diez(): void
    {
        $lector = new LectorDeFechas(new \DateTimeZone('Europe/Madrid'));
        $primera = new FranjaHoraria($lector->leer('2027-03-25T10:00:00+01:00'), $lector->leer('2027-03-25T11:00:00+01:00'));
        $serie = new SerieSemanal($primera, new \DateTimeImmutable('2027-04-01'));

        $semanaDelCambio = $serie->franjas()[1]->inicio()->setTimezone(new \DateTimeZone('Europe/Madrid'));

        self::assertSame('2027-04-01 10:00', $semanaDelCambio->format('Y-m-d H:i'));
    }

    private static function franja(string $inicio, string $fin): FranjaHoraria
    {
        return new FranjaHoraria(new \DateTimeImmutable($inicio), new \DateTimeImmutable($fin));
    }

    /** @param FranjaHoraria[] $franjas */
    private static function dias(array $franjas): array
    {
        return array_map(fn (FranjaHoraria $franja) => $franja->inicio()->format('Y-m-d'), $franjas);
    }
}
