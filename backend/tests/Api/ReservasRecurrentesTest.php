<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\FabricaDeDatos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ReservasRecurrentesTest extends WebTestCase
{
    public function test_una_reserva_semanal_durante_marzo_descarta_la_semana_ocupada(): void
    {
        $cliente = static::createClient();
        $datos = new FabricaDeDatos(static::getContainer()->get(EntityManagerInterface::class));
        $sala = $datos->sala();
        $datos->reserva($sala, '2027-03-18 10:30', '2027-03-18 11:30');

        $cliente->jsonRequest('POST', '/reservas/recurrentes', [
            'sala' => $sala->id(),
            'inicio' => (new \DateTimeImmutable('2027-03-04 10:00'))->format(\DATE_ATOM),
            'fin' => (new \DateTimeImmutable('2027-03-04 11:00'))->format(\DATE_ATOM),
            'hasta' => '2027-03-31',
            'persona' => 'Ana',
            'asistentes' => 4,
        ]);

        self::assertResponseStatusCodeSame(201);
        $respuesta = json_decode($cliente->getResponse()->getContent(), true);
        self::assertSame(['2027-03-04', '2027-03-11', '2027-03-25'], $respuesta['creadas']);
        self::assertSame(['2027-03-18'], $respuesta['descartadas']);
    }

    public function test_una_serie_que_cruza_el_cambio_de_hora_descarta_la_semana_ocupada(): void
    {
        $cliente = static::createClient();
        $datos = new FabricaDeDatos(static::getContainer()->get(EntityManagerInterface::class));
        $sala = $datos->sala();
        $datos->reserva($sala, '2027-04-01 10:00', '2027-04-01 11:00');

        $cliente->jsonRequest('POST', '/reservas/recurrentes', [
            'sala' => $sala->id(),
            'inicio' => '2027-03-25T10:00:00+01:00',
            'fin' => '2027-03-25T11:00:00+01:00',
            'hasta' => '2027-04-01',
            'persona' => 'Ana',
            'asistentes' => 4,
        ]);

        $respuesta = json_decode($cliente->getResponse()->getContent(), true);
        self::assertSame(['2027-04-01'], $respuesta['descartadas']);
    }

    public function test_una_serie_con_todas_las_semanas_ocupadas_responde_409(): void
    {
        $cliente = static::createClient();
        $datos = new FabricaDeDatos(static::getContainer()->get(EntityManagerInterface::class));
        $sala = $datos->sala();
        $datos->reserva($sala, '2027-03-04 10:00', '2027-03-04 11:00');

        $cliente->jsonRequest('POST', '/reservas/recurrentes', [
            'sala' => $sala->id(),
            'inicio' => (new \DateTimeImmutable('2027-03-04 10:00'))->format(\DATE_ATOM),
            'fin' => (new \DateTimeImmutable('2027-03-04 11:00'))->format(\DATE_ATOM),
            'hasta' => '2027-03-04',
            'persona' => 'Ana',
            'asistentes' => 4,
        ]);

        self::assertResponseStatusCodeSame(409);
    }
}
