<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Dominio\RepositorioSalas;
use App\Reservas\Dominio\Sala;

final class RepositorioSalasEnMemoria implements RepositorioSalas
{
    /** @var array<int, Sala> */
    private array $salas = [];

    public function registrar(Sala $sala): int
    {
        $id = count($this->salas) + 1;
        $this->salas[$id] = $sala;

        return $id;
    }

    public function conId(int $id): ?Sala
    {
        return $this->salas[$id] ?? null;
    }
}
