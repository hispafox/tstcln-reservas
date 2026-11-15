<?php

declare(strict_types=1);

namespace App\Salas\Dominio;

final readonly class OcupacionSala
{
    public function __construct(
        public int $sala,
        public string $nombre,
        public int $porcentaje,
    ) {
    }
}
