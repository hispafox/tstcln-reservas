<?php

declare(strict_types=1);

namespace App\Reservas;

final class PersonaPenalizada extends ReservaRechazada
{
    public function __construct(public readonly string $persona)
    {
        parent::__construct('Tienes una penalización por cancelaciones tardías');
    }
}
