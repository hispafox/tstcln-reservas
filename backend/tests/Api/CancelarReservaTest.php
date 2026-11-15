<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\FabricaDeDatos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

final class CancelarReservaTest extends WebTestCase
{
    use ClockSensitiveTrait;

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

    public function test_cancelar_con_dos_horas_justas_de_antelacion_es_a_tiempo(): void
    {
        static::mockTime('2027-03-04 08:00');
        $cliente = static::createClient();
        $datos = new FabricaDeDatos(static::getContainer()->get(EntityManagerInterface::class));
        $reserva = $datos->reserva($datos->sala(), '2027-03-04 10:00', '2027-03-04 11:00');

        $cliente->request('DELETE', '/reservas/'.$reserva->id());

        $respuesta = json_decode($cliente->getResponse()->getContent(), true);
        self::assertSame('cancelada_a_tiempo', $respuesta['estado']);
    }

    public function test_cancelar_una_reserva_que_no_existe_responde_404(): void
    {
        $cliente = static::createClient();

        $cliente->request('DELETE', '/reservas/999999');

        self::assertResponseStatusCodeSame(404);
    }

    public function test_la_respuesta_de_la_cancelacion_llega_como_json(): void
    {
        $cliente = static::createClient();
        $datos = new FabricaDeDatos(static::getContainer()->get(EntityManagerInterface::class));
        $reserva = $datos->reserva($datos->sala(), 'tomorrow 10:00', 'tomorrow 11:00');

        $cliente->request('DELETE', '/reservas/'.$reserva->id());

        self::assertResponseHeaderSame('content-type', 'application/json');
    }
}
