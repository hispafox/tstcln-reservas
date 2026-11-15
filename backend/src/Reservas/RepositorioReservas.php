<?php

declare(strict_types=1);

namespace App\Reservas;

interface RepositorioReservas
{
    public function guardar(Reserva $reserva): void;

    public function confirmadasDeLaSala(Sala $sala): array;
}
