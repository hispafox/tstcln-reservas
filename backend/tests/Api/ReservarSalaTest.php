<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Reservas\Dominio\NotificadorReservas;
use App\Tests\FabricaDeDatos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ReservarSalaTest extends WebTestCase
{
    public function test_reservar_una_sala_ocupada_responde_409(): void
    {
        $cliente = static::createClient();
        $datos = new FabricaDeDatos(static::getContainer()->get(EntityManagerInterface::class));
        $sala = $datos->sala();
        $datos->reserva($sala, 'tomorrow 10:00', 'tomorrow 11:00');

        $cliente->jsonRequest('POST', '/reservas', [
            'sala' => $sala->id(),
            'inicio' => (new \DateTimeImmutable('tomorrow 10:30'))->format(\DATE_ATOM),
            'fin' => (new \DateTimeImmutable('tomorrow 11:30'))->format(\DATE_ATOM),
            'persona' => 'Luis',
            'asistentes' => 3,
        ]);

        self::assertResponseStatusCodeSame(409);
    }

    public function test_reservar_una_sala_libre_responde_201_y_notifica_una_vez(): void
    {
        $cliente = static::createClient();
        $notificador = $this->createMock(NotificadorReservas::class);
        $notificador->expects($this->once())->method('reservaConfirmada');
        static::getContainer()->set(NotificadorReservas::class, $notificador);
        $datos = new FabricaDeDatos(static::getContainer()->get(EntityManagerInterface::class));
        $sala = $datos->sala();

        $cliente->jsonRequest('POST', '/reservas', [
            'sala' => $sala->id(),
            'inicio' => (new \DateTimeImmutable('tomorrow 10:00'))->format(\DATE_ATOM),
            'fin' => (new \DateTimeImmutable('tomorrow 11:00'))->format(\DATE_ATOM),
            'persona' => 'Ana',
            'asistentes' => 4,
        ]);

        self::assertResponseStatusCodeSame(201);
    }

    public function test_reservar_sin_asistentes_responde_400(): void
    {
        $cliente = static::createClient();
        $datos = new FabricaDeDatos(static::getContainer()->get(EntityManagerInterface::class));
        $sala = $datos->sala();

        $cliente->jsonRequest('POST', '/reservas', [
            'sala' => $sala->id(),
            'inicio' => (new \DateTimeImmutable('tomorrow 10:00'))->format(\DATE_ATOM),
            'fin' => (new \DateTimeImmutable('tomorrow 11:00'))->format(\DATE_ATOM),
            'persona' => 'Ana',
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    public function test_reservar_una_sala_que_no_existe_responde_404(): void
    {
        $cliente = static::createClient();

        $cliente->jsonRequest('POST', '/reservas', [
            'sala' => 999,
            'inicio' => (new \DateTimeImmutable('tomorrow 10:00'))->format(\DATE_ATOM),
            'fin' => (new \DateTimeImmutable('tomorrow 11:00'))->format(\DATE_ATOM),
            'persona' => 'Ana',
            'asistentes' => 4,
        ]);

        self::assertResponseStatusCodeSame(404);
    }
}
