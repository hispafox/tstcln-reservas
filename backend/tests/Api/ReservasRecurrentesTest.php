<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Tests\FabricaDeDatos;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[Group('pendiente')]
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
}
