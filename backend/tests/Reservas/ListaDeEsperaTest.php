<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Dominio\Capacidad;
use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\ListaDeEspera;
use App\Reservas\Dominio\PeticionReserva;
use App\Reservas\Dominio\YaEnLaLista;
use PHPUnit\Framework\TestCase;

final class ListaDeEsperaTest extends TestCase
{
    public function test_una_lista_nueva_esta_vacia(): void
    {
        self::assertSame([], (new ListaDeEspera())->porOrdenDeLlegada());
    }

    public function test_quien_se_apunta_primero_es_el_primero_de_la_lista(): void
    {
        $lista = new ListaDeEspera();
        $ana = self::peticion('Ana', '10:00', '11:00');
        $luis = self::peticion('Luis', '10:00', '11:00');

        $lista->apuntar($ana);
        $lista->apuntar($luis);

        self::assertSame([$ana, $luis], $lista->porOrdenDeLlegada());
    }

    public function test_apuntarse_devuelve_el_puesto(): void
    {
        $lista = new ListaDeEspera();
        $lista->apuntar(self::peticion('Ana', '10:00', '11:00'));

        self::assertSame(2, $lista->apuntar(self::peticion('Luis', '10:00', '11:00')));
    }

    public function test_la_misma_persona_no_se_apunta_dos_veces_a_la_misma_franja(): void
    {
        $lista = new ListaDeEspera();
        $lista->apuntar(self::peticion('Ana', '10:00', '11:00'));

        $this->expectException(YaEnLaLista::class);

        $lista->apuntar(self::peticion('Ana', '10:00', '11:00'));
    }

    public function test_la_misma_persona_puede_esperar_dos_franjas_distintas_de_la_misma_sala(): void
    {
        $lista = new ListaDeEspera();
        $lista->apuntar(self::peticion('Ana', '10:00', '11:00'));

        self::assertSame(2, $lista->apuntar(self::peticion('Ana', '10:00', '12:00')));
    }

    public function test_quitar_a_quien_recibe_la_sala_deja_a_los_demas_en_su_orden(): void
    {
        $lista = new ListaDeEspera();
        $ana = self::peticion('Ana', '10:00', '11:00');
        $luis = self::peticion('Luis', '10:00', '11:00');
        $marta = self::peticion('Marta', '10:00', '11:00');
        $lista->apuntar($ana);
        $lista->apuntar($luis);
        $lista->apuntar($marta);

        $lista->quitar($ana);

        self::assertSame([$luis, $marta], $lista->porOrdenDeLlegada());
    }

    private static function peticion(string $persona, string $inicio, string $fin): PeticionReserva
    {
        return new PeticionReserva(
            1,
            new FranjaHoraria(new \DateTimeImmutable("2027-03-04 $inicio"), new \DateTimeImmutable("2027-03-04 $fin")),
            $persona,
            new Capacidad(4),
        );
    }
}
