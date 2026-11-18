<?php

declare(strict_types=1);

namespace App\Reservas\Dominio;

final class YaEnLaLista extends ReservaRechazada
{
    public function __construct(public readonly string $persona)
    {
        parent::__construct('Ya estás en la lista de espera de esa franja');
    }
}
