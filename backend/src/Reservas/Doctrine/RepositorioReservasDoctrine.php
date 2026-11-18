<?php

declare(strict_types=1);

namespace App\Reservas\Doctrine;

use App\Reservas\EstadoReserva;
use App\Reservas\RepositorioReservas;
use App\Reservas\Reserva;
use App\Reservas\Sala;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reserva>
 */
final class RepositorioReservasDoctrine extends ServiceEntityRepository implements RepositorioReservas
{
    public function __construct(ManagerRegistry $registro)
    {
        parent::__construct($registro, Reserva::class);
    }

    public function guardar(Reserva $reserva): void
    {
        $this->getEntityManager()->persist($reserva);
        $this->getEntityManager()->flush();
    }

    public function confirmadasDeLaSala(Sala $sala): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.sala = :sala')
            ->andWhere('r.estado = :estado')
            ->setParameter('sala', $sala)
            ->setParameter('estado', EstadoReserva::Confirmada)
            ->getQuery()
            ->getResult();
    }

    public function tardiasDeLaPersonaDesde(string $persona, \DateTimeImmutable $desde): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.persona = :persona')
            ->andWhere('r.estado = :estado')
            ->andWhere('r.franja.inicio > :desde')
            ->orderBy('r.franja.inicio', 'DESC')
            ->setParameter('persona', $persona)
            ->setParameter('estado', EstadoReserva::CanceladaTarde)
            ->setParameter('desde', $desde)
            ->getQuery()
            ->getResult();
    }
}
