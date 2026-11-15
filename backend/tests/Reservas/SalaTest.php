<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Sala;
use PHPUnit\Framework\TestCase;

final class SalaTest extends TestCase
{
    public function test_una_sala_de_diecinueve_plazas_no_es_grande(): void
    {
        $sala = new Sala('Sala Este', capacidadMaxima: 19);

        self::assertFalse($sala->esGrande());
    }

    public function test_una_sala_de_veinte_plazas_es_grande(): void
    {
        $sala = new Sala('Sala Oeste', capacidadMaxima: 20);

        self::assertTrue($sala->esGrande());
    }
}
