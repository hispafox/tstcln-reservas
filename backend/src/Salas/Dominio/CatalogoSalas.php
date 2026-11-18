<?php

declare(strict_types=1);

namespace App\Salas\Dominio;

use App\Reservas\Dominio\Sala;

interface CatalogoSalas
{
    /**
     * @return list<Sala>
     */
    public function todas(): array;
}
