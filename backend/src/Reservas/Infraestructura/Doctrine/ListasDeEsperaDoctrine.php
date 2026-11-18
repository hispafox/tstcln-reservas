<?php

declare(strict_types=1);

namespace App\Reservas\Infraestructura\Doctrine;

use App\Reservas\Dominio\ListaDeEspera;
use App\Reservas\Dominio\ListasDeEspera;
use App\Reservas\Dominio\Sala;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ListaDeEsperaGuardada>
 */
final class ListasDeEsperaDoctrine extends ServiceEntityRepository implements ListasDeEspera
{
    public function __construct(ManagerRegistry $registro)
    {
        parent::__construct($registro, ListaDeEsperaGuardada::class);
    }

    public function deLaSala(Sala $sala): ListaDeEspera
    {
        return $this->find(self::idDe($sala))?->lista() ?? new ListaDeEspera();
    }

    public function guardar(Sala $sala, ListaDeEspera $lista): void
    {
        $guardada = $this->find(self::idDe($sala));
        if ($guardada === null) {
            $guardada = new ListaDeEsperaGuardada(self::idDe($sala));
            $this->getEntityManager()->persist($guardada);
        }

        $guardada->cambiarA($lista);
        $this->getEntityManager()->flush();
    }

    private static function idDe(Sala $sala): int
    {
        return $sala->id() ?? throw new \LogicException('La sala tiene que estar guardada para tener lista de espera');
    }
}
