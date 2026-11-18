<?php

declare(strict_types=1);

namespace App\Tests\Api;

use App\Reservas\Sala;
use App\Tests\FabricaDeDatos;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

final class PenalizacionHeredadaTest extends WebTestCase
{
    use ClockSensitiveTrait;

    public function test_tres_tardias_de_esta_semana_impiden_reservar_una_sala_grande(): void
    {
        $cliente = static::createClient();
        $sala = $this->salaGrandeConTardias('-1 day 10:00', '-2 days 10:00', '-3 days 10:00');

        $cliente->jsonRequest('POST', '/reservas', self::peticion($sala));

        self::assertResponseStatusCodeSame(409);
        self::assertSame('Tienes una penalización por cancelaciones tardías', self::cuerpo($cliente)['error']);
    }

    public function test_con_tres_tardias_se_puede_reservar_una_sala_pequena(): void
    {
        $cliente = static::createClient();
        $datos = $this->datos();
        $pequena = $datos->sala('Sala Sur', capacidadMaxima: 6);
        $this->salaGrandeConTardias('-1 day 10:00', '-2 days 10:00', '-3 days 10:00');

        $cliente->jsonRequest('POST', '/reservas', self::peticion($pequena));

        self::assertResponseStatusCodeSame(201);
    }

    public function test_dos_tardias_de_hace_tres_semanas_y_una_de_ayer_no_penalizan(): void
    {
        $cliente = static::createClient();
        $sala = $this->salaGrandeConTardias('-3 weeks 10:00', '-3 weeks -1 day 10:00', 'yesterday 10:00');

        $cliente->jsonRequest('POST', '/reservas', self::peticion($sala));

        self::assertResponseStatusCodeSame(201);
    }

    public function test_las_tardias_de_otra_persona_no_cuentan(): void
    {
        $cliente = static::createClient();
        $sala = $this->salaGrandeConTardias('-1 day 10:00', '-2 days 10:00', '-3 days 10:00');

        $cliente->jsonRequest('POST', '/reservas', self::peticion($sala, persona: 'Luis'));

        self::assertResponseStatusCodeSame(201);
    }

    public function test_una_reserva_confirmada_no_cuenta_como_tardia(): void
    {
        $cliente = static::createClient();
        $sala = $this->salaGrandeConTardias('-1 day 10:00', '-2 days 10:00');
        $this->datos()->reserva($sala, '-3 days 10:00', '-3 days 11:00');

        $cliente->jsonRequest('POST', '/reservas', self::peticion($sala));

        self::assertResponseStatusCodeSame(201);
    }

    public function test_una_tardia_de_hace_cuarenta_dias_no_cuenta(): void
    {
        $cliente = static::createClient();
        $sala = $this->salaGrandeConTardias('-1 day 10:00', '-2 days 10:00', '-40 days 10:00');

        $cliente->jsonRequest('POST', '/reservas', self::peticion($sala));

        self::assertResponseStatusCodeSame(201);
    }

    public function test_las_reservas_recurrentes_no_aplican_la_penalizacion(): void
    {
        $cliente = static::createClient();
        $sala = $this->salaGrandeConTardias('-1 day 10:00', '-2 days 10:00', '-3 days 10:00');

        $cliente->jsonRequest('POST', '/reservas/recurrentes', [
            'sala' => $sala->id(),
            'inicio' => '2027-03-15 10:00',
            'fin' => '2027-03-15 11:00',
            'hasta' => '2027-03-31',
            'persona' => 'Ana',
            'asistentes' => 4,
        ]);

        self::assertResponseStatusCodeSame(201);
    }

    public function test_una_franja_que_termina_antes_de_empezar_responde_400(): void
    {
        $cliente = static::createClient();
        $sala = $this->datos()->sala();

        $cliente->jsonRequest('POST', '/reservas', [
            'sala' => $sala->id(),
            'inicio' => '2027-03-15 11:00',
            'fin' => '2027-03-15 10:00',
            'persona' => 'Ana',
            'asistentes' => 4,
        ]);

        self::assertResponseStatusCodeSame(400);
        self::assertSame('Una franja tiene que terminar después de empezar', self::cuerpo($cliente)['error']);
    }

    public function test_con_tres_tardias_una_franja_al_reves_responde_400(): void
    {
        $cliente = static::createClient();
        $sala = $this->salaGrandeConTardias('-1 day 10:00', '-2 days 10:00', '-3 days 10:00');

        $cliente->jsonRequest('POST', '/reservas', [
            'sala' => $sala->id(),
            'inicio' => '2027-03-15 11:00',
            'fin' => '2027-03-15 10:00',
            'persona' => 'Ana',
            'asistentes' => 4,
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    public function test_una_fecha_mal_escrita_responde_500(): void
    {
        $cliente = static::createClient();
        $sala = $this->datos()->sala();

        $cliente->jsonRequest('POST', '/reservas', [
            'sala' => $sala->id(),
            'inicio' => 'el lunes a las diez',
            'fin' => '2027-03-15 11:00',
            'persona' => 'Ana',
            'asistentes' => 4,
        ]);

        self::assertResponseStatusCodeSame(500);
    }

    public function test_la_respuesta_de_una_reserva_creada(): void
    {
        $cliente = static::createClient();
        $sala = $this->datos()->sala('Sala Grande', capacidadMaxima: 20);

        $cliente->jsonRequest('POST', '/reservas', self::peticion($sala));

        $respuesta = self::cuerpo($cliente);
        unset($respuesta['id'], $respuesta['sala']);
        self::assertJsonStringEqualsJsonFile(__DIR__.'/respuestas/reserva-creada.json', json_encode($respuesta));
    }

    public function test_a_los_siete_dias_justos_de_la_tercera_tardia_ya_no_penaliza(): void
    {
        static::mockTime('2027-03-10 12:00');
        $cliente = static::createClient();
        $sala = $this->salaGrandeConTardias('2027-03-05 12:00', '2027-03-04 12:00', '2027-03-03 12:00');

        $cliente->jsonRequest('POST', '/reservas', self::peticion($sala));

        self::assertResponseStatusCodeSame(201);
    }

    public function test_un_minuto_antes_de_los_siete_dias_todavia_penaliza(): void
    {
        static::mockTime('2027-03-10 11:59');
        $cliente = static::createClient();
        $sala = $this->salaGrandeConTardias('2027-03-05 12:00', '2027-03-04 12:00', '2027-03-03 12:00');

        $cliente->jsonRequest('POST', '/reservas', self::peticion($sala));

        self::assertResponseStatusCodeSame(409);
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
