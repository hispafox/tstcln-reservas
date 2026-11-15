<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\FranjaHoraria;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FranjaHorariaTest extends TestCase
{
    #[DataProvider('casosDeSolapamiento')]
    public function test_una_franja_se_solapa_con_otra_si_comparten_algun_minuto(
        string $inicioDeLaOtra,
        string $finDeLaOtra,
        bool $seSolapan,
    ): void {
        $deDiezAOnce = self::franja('10:00', '11:00');

        $resultado = $deDiezAOnce->seSolapaCon(self::franja($inicioDeLaOtra, $finDeLaOtra));

        self::assertSame($seSolapan, $resultado);
    }

    public static function casosDeSolapamiento(): array
    {
        return [
            'acaba antes de que empiece' => ['08:30', '09:30', false],
            'acaba justo cuando empieza' => ['09:00', '10:00', false],
            'comparte solo el primer minuto' => ['09:00', '10:01', true],
            'empieza antes y acaba dentro' => ['09:30', '10:30', true],
            'empieza antes y acaba a la vez' => ['09:00', '11:00', true],
            'empieza a la vez y acaba dentro' => ['10:00', '10:30', true],
            'queda dentro' => ['10:15', '10:45', true],
            'es idéntica' => ['10:00', '11:00', true],
            'empieza dentro y acaba a la vez' => ['10:30', '11:00', true],
            'empieza a la vez y acaba después' => ['10:00', '12:00', true],
            'la envuelve' => ['09:00', '12:00', true],
            'empieza dentro y acaba después' => ['10:30', '11:30', true],
            'comparte solo el último minuto' => ['10:59', '12:00', true],
            'empieza justo cuando acaba' => ['11:00', '12:00', false],
            'empieza después de que acabe' => ['11:30', '12:30', false],
        ];
    }

    public function test_la_misma_franja_en_dias_distintos_no_se_solapa(): void
    {
        $elJueves = self::franja('10:00', '11:00');

        $seSolapan = $elJueves->seSolapaCon(self::franja('10:00', '11:00', '2027-03-05'));

        self::assertFalse($seSolapan);
    }

    #[DataProvider('casosDeContencion')]
    public function test_una_franja_contiene_a_otra_si_la_otra_no_se_sale_por_ningun_extremo(
        string $inicioDeLaOtra,
        string $finDeLaOtra,
        bool $laContiene,
    ): void {
        $deOchoAVeinte = self::franja('08:00', '20:00');

        $resultado = $deOchoAVeinte->contiene(self::franja($inicioDeLaOtra, $finDeLaOtra));

        self::assertSame($laContiene, $resultado);
    }

    public static function casosDeContencion(): array
    {
        return [
            'empieza justo cuando empieza' => ['08:00', '09:00', true],
            'queda dentro' => ['10:00', '11:00', true],
            'acaba justo cuando acaba' => ['19:00', '20:00', true],
            'empieza antes' => ['07:30', '08:30', false],
            'acaba después' => ['19:30', '20:30', false],
        ];
    }

    #[DataProvider('franjasNoValidas')]
    public function test_una_franja_tiene_que_terminar_despues_de_empezar(string $inicio, string $fin): void
    {
        $this->expectException(\InvalidArgumentException::class);

        self::franja($inicio, $fin);
    }

    public static function franjasNoValidas(): array
    {
        return [
            'termina antes de empezar' => ['11:00', '10:00'],
            'dura cero minutos' => ['10:00', '10:00'],
        ];
    }

    private static function franja(string $inicio, string $fin, string $dia = '2027-03-04'): FranjaHoraria
    {
        return new FranjaHoraria(
            new \DateTimeImmutable("$dia $inicio"),
            new \DateTimeImmutable("$dia $fin"),
        );
    }
}
