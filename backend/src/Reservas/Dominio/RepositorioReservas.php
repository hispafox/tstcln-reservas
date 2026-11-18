<?php

declare(strict_types=1);

namespace App\Reservas\Dominio;

interface RepositorioReservas
{
    public function guardar(Reserva $reserva): void;

    public function conId(int $id): ?Reserva;

    /**
     * @return list<Reserva>
     */
    public function confirmadasDeLaSala(Sala $sala): array;

    /**
     * Las cancelaciones tardías de la persona que empiezan después de $desde, de la más reciente a la más antigua.
     *
     * @return list<Reserva>
     */
    public function tardiasDeLaPersonaDesde(string $persona, \DateTimeImmutable $desde): array;
}
