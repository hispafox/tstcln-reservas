<?php

declare(strict_types=1);

namespace App\Reservas\Dominio;

interface RepositorioSalas
{
    public function conId(int $id): ?Sala;
}
