<?php

declare(strict_types=1);

namespace App\Reservas\Infraestructura\Doctrine;

use App\Reservas\Dominio\RepositorioSalas;
use App\Reservas\Dominio\Sala;
use Doctrine\ORM\EntityManagerInterface;

final class RepositorioSalasDoctrine implements RepositorioSalas
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function conId(int $id): ?Sala
    {
        return $this->em->find(Sala::class, $id);
    }
}
