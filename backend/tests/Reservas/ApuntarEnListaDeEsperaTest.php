<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Aplicacion\ApuntarEnListaDeEspera;
use App\Reservas\Dominio\Capacidad;
use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\FranjaLibre;
use App\Reservas\Dominio\PeticionReserva;
use App\Reservas\Dominio\Reserva;
use App\Reservas\Dominio\Sala;
use PHPUnit\Framework\TestCase;

final class ApuntarEnListaDeEsperaTest extends TestCase
{
    public function test_apuntarse_a_una_franja_libre_lanza_franja_libre(): void
    {
        $salas = new RepositorioSalasEnMemoria();
        $norte = new Sala('Sala Norte', capacidadMaxima: 10);
        $idNorte = $salas->registrar($norte);
        $reservas = new RepositorioReservasEnMemoria();
        $reservas->guardar(new Reserva($norte, self::franja('12:00', '13:00'), 'Marta', new Capacidad(4)));
        $apuntar = new ApuntarEnListaDeEspera($salas, $reservas, new ListasDeEsperaEnMemoria());

        $this->expectException(FranjaLibre::class);

        $apuntar->ejecutar(new PeticionReserva($idNorte, self::franja('10:00', '11:00'), 'Ana', new Capacidad(4)));
    }

    private static function franja(string $inicio, string $fin): FranjaHoraria
    {
        return new FranjaHoraria(new \DateTimeImmutable("2027-03-04 $inicio"), new \DateTimeImmutable("2027-03-04 $fin"));
    }
}
