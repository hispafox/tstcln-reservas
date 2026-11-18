<?php

declare(strict_types=1);

namespace App\Tests\Integracion;

use App\Reservas\Aplicacion\CancelarReserva;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ContenedorTest extends KernelTestCase
{
    public function test_el_contenedor_sabe_construir_la_operacion_de_cancelar(): void
    {
        self::bootKernel();

        $cancelar = static::getContainer()->get(CancelarReserva::class);

        self::assertInstanceOf(CancelarReserva::class, $cancelar);
    }
}
