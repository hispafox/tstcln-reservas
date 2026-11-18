<?php

declare(strict_types=1);

namespace App\Reservas\Dominio;

/**
 * Las peticiones que esperan a que se libere una sala, por orden de llegada.
 * Una petición en espera no es una reserva: es una PeticionReserva sin atender.
 */
final class ListaDeEspera
{
    /** @var list<PeticionReserva> */
    private array $peticiones = [];

    /**
     * @param list<PeticionReserva> $peticiones en el orden en que llegaron
     */
    public function __construct(array $peticiones = [])
    {
        foreach ($peticiones as $peticion) {
            $this->apuntar($peticion);
        }
    }

    public function apuntar(PeticionReserva $peticion): int
    {
        foreach ($this->peticiones as $apuntada) {
            if ($apuntada->persona === $peticion->persona && self::mismaFranja($apuntada->franja, $peticion->franja)) {
                throw new YaEnLaLista($peticion->persona);
            }
        }

        $this->peticiones[] = $peticion;

        return count($this->peticiones);
    }

    /**
     * @return list<PeticionReserva>
     */
    public function porOrdenDeLlegada(): array
    {
        return $this->peticiones;
    }

    public function quitar(PeticionReserva $peticion): void
    {
        $this->peticiones = array_values(array_filter(
            $this->peticiones,
            fn(PeticionReserva $apuntada) => $apuntada !== $peticion,
        ));
    }

    private static function mismaFranja(FranjaHoraria $una, FranjaHoraria $otra): bool
    {
        return $una->inicio() == $otra->inicio() && $una->fin() == $otra->fin();
    }
}
