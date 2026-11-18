<?php

declare(strict_types=1);

namespace App\Reservas\Infraestructura;

use App\Reservas\Dominio\DatoNoValido;
use App\Reservas\Dominio\NoEncontrada;
use App\Reservas\Dominio\ReservaRechazada;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

#[AsEventListener]
final class TraductorDeErrores
{
    public function __invoke(ExceptionEvent $evento): void
    {
        $error = $evento->getThrowable();

        $estado = match (true) {
            $error instanceof NoEncontrada => 404,
            $error instanceof ReservaRechazada => 409,
            $error instanceof DatoNoValido => 400,
            default => null,
        };

        if ($estado !== null) {
            $evento->setResponse(new JsonResponse(['error' => $error->getMessage()], $estado));
        }
    }
}
