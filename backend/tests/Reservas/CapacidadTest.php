<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Dominio\Capacidad;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CapacidadTest extends TestCase
{
    public function test_no_hay_capacidad_de_cero_asistentes(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Capacidad(0);
    }

    public function test_no_hay_capacidad_de_asistentes_negativos(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Capacidad(-1);
    }

    public function test_un_asistente_es_una_capacidad(): void
    {
        self::assertSame(1, (new Capacidad(1))->valor());
    }

    #[DataProvider('casosDeCabeEntre')]
    public function test_cabe_entre_el_minimo_y_el_maximo_incluidos(int $asistentes, bool $cabe): void
    {
        self::assertSame($cabe, (new Capacidad($asistentes))->cabeEntre(4, 10));
    }

    public static function casosDeCabeEntre(): array
    {
        return [
            'uno menos que el mínimo' => [3, false],
            'justo el mínimo' => [4, true],
            'justo el máximo' => [10, true],
            'uno más que el máximo' => [11, false],
        ];
    }
}
