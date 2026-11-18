<?php

declare(strict_types=1);

namespace App\Reservas\Infraestructura;

use App\Reservas\Aplicacion\CancelarReserva;
use App\Reservas\Aplicacion\CrearReservasRecurrentes;
use App\Reservas\Aplicacion\PeticionReserva;
use App\Reservas\Aplicacion\ReservarSala;
use App\Reservas\Dominio\Capacidad;
use App\Reservas\Dominio\DatoNoValido;
use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\Reserva;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class ReservasController
{
    #[Route('/reservas', methods: ['POST'])]
    public function reservar(Request $peticion, ReservarSala $reservar): JsonResponse
    {
        $reserva = $reservar->ejecutar(self::peticionDe($peticion->toArray()));

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
    public function cancelar(int $id, CancelarReserva $cancelar): JsonResponse
    {
        return new JsonResponse(self::comoJson($cancelar->ejecutar($id)));
    }

    #[Route('/reservas/recurrentes', methods: ['POST'])]
    public function crearRecurrentes(
        Request $peticion,
        CrearReservasRecurrentes $crear,
        LectorDeFechas $lector,
    ): JsonResponse {
        $cuerpo = $peticion->toArray();

        $resultado = $crear->ejecutar(self::peticionDe($cuerpo, $lector), $lector->leer($cuerpo['hasta']));

        return new JsonResponse([
            'creadas' => array_map(fn (FranjaHoraria $franja) => $franja->inicio()->format('Y-m-d'), $resultado->libres()),
            'descartadas' => array_map(fn (FranjaHoraria $franja) => $franja->inicio()->format('Y-m-d'), $resultado->descartadas()),
        ], 201);
    }

    private static function peticionDe(array $cuerpo, ?LectorDeFechas $lector = null): PeticionReserva
    {
        foreach (['sala', 'inicio', 'fin', 'persona', 'asistentes'] as $campo) {
            if (!isset($cuerpo[$campo])) {
                throw new DatoNoValido(sprintf('Falta el campo %s', $campo));
            }
        }

        if (!is_int($cuerpo['sala']) || !is_int($cuerpo['asistentes'])) {
            throw new DatoNoValido('La sala y los asistentes tienen que ser números enteros');
        }

        if (!is_string($cuerpo['persona']) || !is_string($cuerpo['inicio']) || !is_string($cuerpo['fin'])) {
            throw new DatoNoValido('La persona y las fechas tienen que ser textos');
        }

        $fecha = fn (string $texto) => $lector?->leer($texto) ?? new \DateTimeImmutable($texto);

        return new PeticionReserva(
            $cuerpo['sala'],
            new FranjaHoraria($fecha($cuerpo['inicio']), $fecha($cuerpo['fin'])),
            $cuerpo['persona'],
            new Capacidad($cuerpo['asistentes']),
        );
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
