<?php

declare(strict_types=1);

namespace App\Salas\Infraestructura;

use App\Reservas\FranjaHoraria;
use App\Reservas\Reserva;
use App\Reservas\Sala;
use App\Salas\Dominio\AgendaReservas;
use Doctrine\ORM\EntityManagerInterface;

final class AgendaReservasDoctrine implements AgendaReservas
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function franjasConfirmadas(Sala $sala, FranjaHoraria $periodo): array
    {
        $reservas = $this->em->createQueryBuilder()
            ->select('r')
            ->from(Reserva::class, 'r')
            ->andWhere('r.sala = :sala')
            ->andWhere('r.estado = :estado')
            ->andWhere('r.franja.inicio < :fin')
            ->andWhere('r.franja.fin > :inicio')
            ->setParameter('sala', $sala)
            ->setParameter('estado', 'confirmada')
            ->setParameter('inicio', $periodo->inicio())
            ->setParameter('fin', $periodo->fin())
            ->getQuery()
            ->getResult();

        return array_map(fn (Reserva $reserva) => $reserva->franja(), $reservas);
    }
}
