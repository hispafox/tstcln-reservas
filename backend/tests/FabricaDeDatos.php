<?php

declare(strict_types=1);

namespace App\Tests;

use App\Reservas\Dominio\Capacidad;
use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\Reserva;
use App\Reservas\Dominio\Sala;
use Doctrine\ORM\EntityManagerInterface;

final class FabricaDeDatos
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function sala(string $nombre = 'Sala Norte', int $capacidadMaxima = 10): Sala
    {
        $sala = new Sala($nombre, capacidadMaxima: $capacidadMaxima);
        $this->entityManager->persist($sala);
        $this->entityManager->flush();

        return $sala;
    }

    public function reserva(Sala $sala, string $inicio, string $fin, string $persona = 'Ana', int $asistentes = 4): Reserva
    {
        $reserva = new Reserva(
            $sala,
            new FranjaHoraria(new \DateTimeImmutable($inicio), new \DateTimeImmutable($fin)),
            $persona,
            new Capacidad($asistentes),
        );
        $this->entityManager->persist($reserva);
        $this->entityManager->flush();

        return $reserva;
    }
}
