<?php

declare(strict_types=1);

namespace App\Tests\Integracion;

use App\Reservas\Capacidad;
use App\Reservas\Correo\NotificadorReservasPorCorreo;
use App\Reservas\FranjaHoraria;
use App\Reservas\Reserva;
use App\Reservas\Sala;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class NotificadorReservasPorCorreoTest extends KernelTestCase
{
    public function test_una_serie_se_avisa_con_un_solo_correo_que_lleva_todas_las_fechas(): void
    {
        self::bootKernel();
        $notificador = static::getContainer()->get(NotificadorReservasPorCorreo::class);
        $sala = new Sala('Sala Norte', capacidadMaxima: 10);

        $notificador->serieConfirmada([
            new Reserva($sala, self::franja('2027-03-04 10:00', '2027-03-04 11:00'), 'Ana', new Capacidad(4)),
            new Reserva($sala, self::franja('2027-03-11 10:00', '2027-03-11 11:00'), 'Ana', new Capacidad(4)),
            new Reserva($sala, self::franja('2027-03-25 10:00', '2027-03-25 11:00'), 'Ana', new Capacidad(4)),
        ]);

        self::assertEmailCount(1);
        $correo = self::getMailerMessage();
        self::assertEmailTextBodyContains($correo, '04/03/2027, 11/03/2027, 25/03/2027');
    }

    private static function franja(string $inicio, string $fin): FranjaHoraria
    {
        return new FranjaHoraria(new \DateTimeImmutable($inicio), new \DateTimeImmutable($fin));
    }
}
