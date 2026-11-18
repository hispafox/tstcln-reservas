<?php

declare(strict_types=1);

namespace App\Salas\Infraestructura;

use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\Sala;
use App\Salas\Aplicacion\CalcularOcupacion;
use App\Salas\Aplicacion\ListarSalas;
use App\Salas\Dominio\OcupacionSala;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final class SalasController
{
    #[Route('/salas', methods: ['GET'])]
    public function listar(ListarSalas $listar): JsonResponse
    {
        return new JsonResponse(array_map(fn (Sala $sala) => [
            'id' => $sala->id(),
            'nombre' => $sala->nombre(),
            'capacidadMaxima' => $sala->capacidadMaxima(),
            'capacidadMinima' => $sala->capacidadMinima(),
            'equipamiento' => $sala->equipamiento(),
        ], $listar->ejecutar()));
    }

    #[Route('/ocupacion', methods: ['GET'])]
    public function ocupacion(Request $peticion, CalcularOcupacion $calcular): JsonResponse
    {
        $desde = self::dia($peticion->query->getString('desde'));
        $hasta = self::dia($peticion->query->getString('hasta'));
        if ($desde === null || $hasta === null || $hasta < $desde) {
            return new JsonResponse(['error' => 'Indica desde y hasta como AAAA-MM-DD, con hasta igual o posterior a desde'], 400);
        }

        $periodo = new FranjaHoraria($desde, $hasta->modify('+1 day'));

        return new JsonResponse(array_map(fn (OcupacionSala $ocupacion) => [
            'sala' => $ocupacion->sala,
            'nombre' => $ocupacion->nombre,
            'porcentaje' => $ocupacion->porcentaje,
        ], $calcular->ejecutar($periodo)));
    }

    private static function dia(string $texto): ?\DateTimeImmutable
    {
        $dia = \DateTimeImmutable::createFromFormat('!Y-m-d', $texto);

        return $dia !== false && $dia->format('Y-m-d') === $texto ? $dia : null;
    }
}
