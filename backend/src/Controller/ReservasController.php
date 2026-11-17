<?php

declare(strict_types=1);

namespace App\Controller;

use App\Reservas\Capacidad;
use App\Reservas\CrearReservasRecurrentes;
use App\Reservas\FranjaHoraria;
use App\Reservas\LectorDeFechas;
use App\Reservas\Reserva;
use App\Reservas\ReservaNoEncontrada;
use App\Reservas\Sala;
use App\Reservas\SalaNoEncontrada;
use App\Reservas\ServicioCancelacion;
use App\Reservas\ServicioReservas;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ReservasController
{
    #[Route('/reservas', methods: ['POST'])]
    public function reservar(
        Request $peticion,
        EntityManagerInterface $em,
        ServicioReservas $servicio,
    ): JsonResponse {
        $cuerpo = $peticion->toArray();
        $sala = $em->find(Sala::class, $cuerpo['sala']) ?? throw new SalaNoEncontrada($cuerpo['sala']);

        $reserva = $servicio->reservar(
            $sala,
            new FranjaHoraria(new \DateTimeImmutable($cuerpo['inicio']), new \DateTimeImmutable($cuerpo['fin'])),
            $cuerpo['persona'],
            new Capacidad($cuerpo['asistentes']),
        );

        return new JsonResponse(self::comoJson($reserva), 201);
    }

    #[Route('/reservas', methods: ['GET'])]
    public function deUnaPersona(Request $peticion, EntityManagerInterface $em): JsonResponse
    {
        $persona = $peticion->query->getString('persona');
        if ($persona === '') {
            return new JsonResponse(['error' => 'Indica la persona'], 400);
        }

        $reservas = $em->createQueryBuilder()
            ->select('r')
            ->from(Reserva::class, 'r')
            ->andWhere('r.persona = :persona')
            ->orderBy('r.franja.inicio')
            ->setParameter('persona', $persona)
            ->getQuery()
            ->getResult();

        return new JsonResponse(array_map(self::comoJson(...), $reservas));
    }

    #[Route('/reservas/{id}', methods: ['DELETE'])]
    public function cancelar(
        int $id,
        EntityManagerInterface $em,
        ServicioCancelacion $servicio,
    ): JsonResponse {
        $reserva = $em->find(Reserva::class, $id) ?? throw new ReservaNoEncontrada($id);

        $servicio->cancelar($reserva);

        return new JsonResponse(self::comoJson($reserva));
    }

    #[Route('/reservas/recurrentes', methods: ['POST'])]
    public function crearRecurrentes(
        Request $peticion,
        EntityManagerInterface $em,
        CrearReservasRecurrentes $crear,
        LectorDeFechas $lector,
    ): JsonResponse {
        $cuerpo = $peticion->toArray();
        $sala = $em->find(Sala::class, $cuerpo['sala']) ?? throw new SalaNoEncontrada($cuerpo['sala']);

        $resultado = $crear->ejecutar(
            $sala,
            new FranjaHoraria($lector->leer($cuerpo['inicio']), $lector->leer($cuerpo['fin'])),
            $lector->leer($cuerpo['hasta']),
            $cuerpo['persona'],
            new Capacidad($cuerpo['asistentes']),
        );

        return new JsonResponse([
            'creadas' => array_map(fn (FranjaHoraria $franja) => $franja->inicio()->format('Y-m-d'), $resultado->libres()),
            'descartadas' => array_map(fn (FranjaHoraria $franja) => $franja->inicio()->format('Y-m-d'), $resultado->descartadas()),
        ], 201);
    }

    private static function comoJson(Reserva $reserva): array
    {
        return [
            'id' => $reserva->id(),
            'sala' => $reserva->sala()->id(),
            'inicio' => $reserva->franja()->inicio()->format(\DATE_ATOM),
            'fin' => $reserva->franja()->fin()->format(\DATE_ATOM),
            'persona' => $reserva->persona(),
            'asistentes' => $reserva->asistentes()->valor(),
            'estado' => $reserva->estado()->value,
        ];
    }
}
