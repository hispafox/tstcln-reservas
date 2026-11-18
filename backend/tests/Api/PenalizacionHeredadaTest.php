<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Reservas\Sala;
use App\Tests\FabricaDeDatos;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PenalizacionHeredadaTest extends WebTestCase
{
    #[Group('pendiente')]
    public function test_dos_tardias_de_hace_tres_semanas_y_una_de_ayer(): void
    {
        $cliente = static::createClient();
        $sala = $this->salaGrandeConTardias('-3 weeks 10:00', '-3 weeks -1 day 10:00', 'yesterday 10:00');

        $cliente->jsonRequest('POST', '/reservas', self::peticion($sala));

        self::assertResponseStatusCodeSame(0);
    }

    private function salaGrandeConTardias(string ...$inicios): Sala
    {
        $datos = $this->datos();
        $sala = $datos->sala('Sala Grande', capacidadMaxima: 20);
        foreach ($inicios as $inicio) {
            $datos->reserva($sala, $inicio, $inicio.' +1 hour')->cancelarTarde();
        }
        static::getContainer()->get(EntityManagerInterface::class)->flush();

        return $sala;
    }

    private function datos(): FabricaDeDatos
    {
        return new FabricaDeDatos(static::getContainer()->get(EntityManagerInterface::class));
    }

    private static function peticion(Sala $sala, string $persona = 'Ana'): array
    {
        return [
            'sala' => $sala->id(),
            'inicio' => '2027-03-15 10:00',
            'fin' => '2027-03-15 11:00',
            'persona' => $persona,
            'asistentes' => 4,
        ];
    }

    private static function cuerpo(KernelBrowser $cliente): array
    {
        return json_decode($cliente->getResponse()->getContent(), true);
    }
}
