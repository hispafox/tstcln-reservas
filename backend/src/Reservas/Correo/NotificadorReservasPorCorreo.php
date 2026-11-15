<?php

declare(strict_types=1);

namespace App\Reservas\Correo;

use App\Reservas\NotificadorReservas;
use App\Reservas\Reserva;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class NotificadorReservasPorCorreo implements NotificadorReservas
{
    private const REMITENTE = 'reservas@example.com';
    private const DESTINATARIO = 'recepcion@example.com';

    public function __construct(
        private MailerInterface $correo,
    ) {
    }

    public function reservaConfirmada(Reserva $reserva): void
    {
        $franja = $reserva->franja();

        $this->correo->send((new Email())
            ->from(self::REMITENTE)
            ->to(self::DESTINATARIO)
            ->subject('Reserva confirmada: '.$reserva->sala()->nombre())
            ->text(sprintf(
                "%s, de %s a %s, a nombre de %s, para %d asistentes.",
                $reserva->sala()->nombre(),
                $franja->inicio()->format('d/m/Y H:i'),
                $franja->fin()->format('H:i'),
                $reserva->persona(),
                $reserva->asistentes(),
            )));
    }

    /** @param Reserva[] $reservas */
    public function serieConfirmada(array $reservas): void
    {
        $primera = $reservas[0];
        $fechas = array_map(
            fn (Reserva $reserva) => $reserva->franja()->inicio()->format('d/m/Y'),
            $reservas,
        );

        $this->correo->send((new Email())
            ->from(self::REMITENTE)
            ->to(self::DESTINATARIO)
            ->subject('Reserva semanal confirmada: '.$primera->sala()->nombre())
            ->text(sprintf(
                "%s, de %s a %s, a nombre de %s, para %d asistentes, los días %s.",
                $primera->sala()->nombre(),
                $primera->franja()->inicio()->format('H:i'),
                $primera->franja()->fin()->format('H:i'),
                $primera->persona(),
                $primera->asistentes(),
                implode(', ', $fechas),
            )));
    }
}
