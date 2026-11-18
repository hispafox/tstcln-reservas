<?php

declare(strict_types=1);

namespace App\Reservas\Infraestructura\Doctrine;

use App\Reservas\Dominio\Capacidad;
use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\ListaDeEspera;
use App\Reservas\Dominio\PeticionReserva;

/**
 * Una fila de la tabla lista_de_espera: las peticiones en espera de una sala, por orden de llegada.
 */
final class ListaDeEsperaGuardada
{
    /** @var list<array{inicio: string, fin: string, persona: string, asistentes: int}> */
    private array $peticiones = [];

    public function __construct(private int $sala) {}

    public function lista(): ListaDeEspera
    {
        return new ListaDeEspera(array_map(
            fn(array $peticion) => new PeticionReserva(
                $this->sala,
                new FranjaHoraria(new \DateTimeImmutable($peticion['inicio']), new \DateTimeImmutable($peticion['fin'])),
                $peticion['persona'],
                new Capacidad($peticion['asistentes']),
            ),
            $this->peticiones,
        ));
    }

    public function cambiarA(ListaDeEspera $lista): void
    {
        $this->peticiones = array_map(
            fn(PeticionReserva $peticion) => [
                'inicio' => $peticion->franja->inicio()->format(\DATE_ATOM),
                'fin' => $peticion->franja->fin()->format(\DATE_ATOM),
                'persona' => $peticion->persona,
                'asistentes' => $peticion->asistentes->valor(),
            ],
            $lista->porOrdenDeLlegada(),
        );
    }
}
