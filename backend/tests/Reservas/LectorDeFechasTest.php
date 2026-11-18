<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Infraestructura\LectorDeFechas;
use PHPUnit\Framework\TestCase;

final class LectorDeFechasTest extends TestCase
{
    public function test_una_fecha_con_desfase_se_lee_en_la_zona_de_la_aplicacion(): void
    {
        $lector = new LectorDeFechas(new \DateTimeZone('Europe/Madrid'));

        $fecha = $lector->leer('2027-03-25T10:00:00+01:00');

        self::assertSame('Europe/Madrid', $fecha->getTimezone()->getName());
        self::assertSame('2027-03-25 10:00', $fecha->format('Y-m-d H:i'));
    }
}
