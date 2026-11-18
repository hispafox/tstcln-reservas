<?php

declare(strict_types=1);

namespace App\Reservas\Dominio;

final class ReservaNoEncontrada extends NoEncontrada
{
    public function __construct(public readonly int $id)
    {
        parent::__construct(sprintf('La reserva %d no existe', $id));
    }
}
