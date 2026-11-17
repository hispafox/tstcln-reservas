<?php

declare(strict_types=1);

namespace App\Reservas;

final class SalaNoEncontrada extends NoEncontrada
{
    public function __construct(public readonly int $id)
    {
        parent::__construct(sprintf('La sala %d no existe', $id));
    }
}
