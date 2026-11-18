<?php

declare(strict_types=1);

namespace App\Salas\Aplicacion;

use App\Reservas\Dominio\Sala;
use App\Salas\Dominio\CatalogoSalas;

final class ListarSalas
{
    public function __construct(
        private CatalogoSalas $catalogo,
    ) {
    }

    /**
     * @return list<Sala>
     */
    public function ejecutar(): array
    {
        return $this->catalogo->todas();
    }
}
