<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\FabricaDeDatos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CancelarReservaTest extends WebTestCase
{
    public function test_cancelar_una_reserva_de_manana_la_deja_cancelada_a_tiempo(): void
    {
        $cliente = static::createClient();
        $datos = new FabricaDeDatos(static::getContainer()->get(EntityManagerInterface::class));
        $reserva = $datos->reserva($datos->sala(), 'tomorrow 10:00', 'tomorrow 11:00');

        $cliente->request('DELETE', '/reservas/'.$reserva->id());

        self::assertResponseIsSuccessful();
        $respuesta = json_decode($cliente->getResponse()->getContent(), true);
        self::assertSame('cancelada_a_tiempo', $respuesta['estado']);
    }
}
