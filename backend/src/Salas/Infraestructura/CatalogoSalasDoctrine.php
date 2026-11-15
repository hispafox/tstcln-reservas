<?php

declare(strict_types=1);

namespace App\Salas\Infraestructura;

use App\Reservas\Sala;
use App\Salas\Dominio\CatalogoSalas;
use Doctrine\ORM\EntityManagerInterface;

final class CatalogoSalasDoctrine implements CatalogoSalas
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function todas(): array
    {
        return $this->em->getRepository(Sala::class)->findBy([], ['nombre' => 'ASC']);
    }
}
