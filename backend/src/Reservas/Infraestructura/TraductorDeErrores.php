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
            $cuerpo = ['error' => $error->getMessage()];
            if ($error instanceof ReservaRechazada) {
                $cuerpo['codigo'] = self::codigoDe($error);
            }

            $evento->setResponse(new JsonResponse($cuerpo, $estado));
        }
    }

    /**
     * El nombre de la clase del rechazo en snake_case: SalaNoDisponible da sala_no_disponible.
     */
    private static function codigoDe(ReservaRechazada $rechazo): string
    {
        $clase = (new \ReflectionClass($rechazo))->getShortName();

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $clase) ?? $clase);
    }
}
