<?php

declare(strict_types=1);

namespace App\Tests\Integracion;

use App\Reservas\Aplicacion\ServicioCancelacion;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ContenedorTest extends KernelTestCase
{
    public function test_el_contenedor_sabe_construir_el_servicio_de_cancelacion(): void
    {
        self::bootKernel();

        $servicio = static::getContainer()->get(ServicioCancelacion::class);

        self::assertInstanceOf(ServicioCancelacion::class, $servicio);
    }
}
