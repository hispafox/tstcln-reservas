<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\FabricaDeDatos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ListaDeEsperaTest extends WebTestCase
{
    public function test_apuntarse_a_una_franja_ocupada_responde_201_con_el_puesto(): void
    {
        $cliente = static::createClient();
        $datos = self::datos();
        $sala = $datos->sala();
        $datos->reserva($sala, 'tomorrow 10:00', 'tomorrow 11:00');

        self::apuntar($cliente, $sala->id(), 'tomorrow 10:00', 'tomorrow 11:00');

        self::assertResponseStatusCodeSame(201);
        self::assertJsonStringEqualsJsonString('{"posicion": 1}', $cliente->getInternalResponse()->getContent());
    }

    public function test_apuntarse_a_una_franja_libre_responde_409_con_el_codigo_del_rechazo(): void
    {
        $cliente = static::createClient();
        $datos = self::datos();
        $sala = $datos->sala();
        $datos->reserva($sala, 'tomorrow 12:00', 'tomorrow 13:00');

        self::apuntar($cliente, $sala->id(), 'tomorrow 10:00', 'tomorrow 11:00');

        self::assertResponseStatusCodeSame(409);
        $respuesta = json_decode($cliente->getInternalResponse()->getContent(), true);
        self::assertIsArray($respuesta);
        self::assertSame('franja_libre', $respuesta['codigo']);
    }

    private static function apuntar(KernelBrowser $cliente, ?int $sala, string $inicio, string $fin): void
    {
        $cliente->jsonRequest('POST', '/lista-espera', [
            'sala' => $sala,
            'inicio' => (new \DateTimeImmutable($inicio))->format(\DATE_ATOM),
            'fin' => (new \DateTimeImmutable($fin))->format(\DATE_ATOM),
            'persona' => 'Luis',
            'asistentes' => 3,
        ]);
    }

    private static function datos(): FabricaDeDatos
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        self::assertInstanceOf(EntityManagerInterface::class, $em);

        return new FabricaDeDatos($em);
    }
}
