<?php

declare(strict_types=1);

namespace App\Reservas\Aplicacion;

use App\Reservas\Dominio\ListasDeEspera;
use App\Reservas\Dominio\RepositorioReservas;
use App\Reservas\Dominio\Reserva;
use App\Reservas\Dominio\ReservaNoEncontrada;
use App\Reservas\Dominio\ReservaRechazada;
use App\Reservas\Dominio\Sala;
use Psr\Clock\ClockInterface;

final class CancelarReserva
{
    public function __construct(
        private RepositorioReservas $reservas,
        private ClockInterface $reloj,
        private ListasDeEspera $listas,
        private ReservarSala $reservar,
    ) {}

    public function ejecutar(int $id): Reserva
    {
        $reserva = $this->reservas->conId($id) ?? throw new ReservaNoEncontrada($id);

        $reserva->cancelar($this->reloj->now());
        $this->reservas->guardar($reserva);
        $this->atenderLaListaDeEspera($reserva->sala());

        return $reserva;
    }

    /**
     * La sala pasa a la primera petición, por orden de llegada, que ReservarSala acepte:
     * si su franja sigue ocupada o una regla la rechaza, se queda en la lista y se prueba la siguiente.
     */
    private function atenderLaListaDeEspera(Sala $sala): void
    {
        $lista = $this->listas->deLaSala($sala);

        foreach ($lista->porOrdenDeLlegada() as $peticion) {
            try {
                $this->reservar->ejecutar($peticion);
            } catch (ReservaRechazada) {
                continue;
            }

            $lista->quitar($peticion);
            $this->listas->guardar($sala, $lista);

            return;
        }
    }
}
