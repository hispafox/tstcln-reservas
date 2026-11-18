<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\Aplicacion\CancelarReserva;
use App\Reservas\Aplicacion\ReservarSala;
use App\Reservas\Dominio\Capacidad;
use App\Reservas\Dominio\EstadoReserva;
use App\Reservas\Dominio\FranjaHoraria;
use App\Reservas\Dominio\NotificadorReservas;
use App\Reservas\Dominio\PeticionReserva;
use App\Reservas\Dominio\ReglaDePenalizacion;
use App\Reservas\Dominio\Reserva;
use App\Reservas\Dominio\Sala;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

final class CancelarReservaTest extends TestCase
{
    private RepositorioSalasEnMemoria $salas;
    private RepositorioReservasEnMemoria $reservas;
    private ListasDeEsperaEnMemoria $listas;

    protected function setUp(): void
    {
        $this->salas = new RepositorioSalasEnMemoria();
        $this->reservas = new RepositorioReservasEnMemoria();
        $this->listas = new ListasDeEsperaEnMemoria();
    }

    public function test_una_reserva_cancelada_con_mucha_antelacion_queda_cancelada_a_tiempo(): void
    {
        $reserva = $this->reservaGuardada(new Sala('Sala Norte', capacidadMaxima: 10), 'Ana', '10:00', '11:00');

        $this->cancelar($this->createStub(NotificadorReservas::class))->ejecutar($this->reservas->idDe($reserva));

        self::assertSame(EstadoReserva::CanceladaATiempo, $reserva->estado());
    }

    public function test_al_cancelar_la_primera_peticion_con_la_franja_libre_recibe_la_sala_sale_de_la_lista_y_recibe_el_aviso(): void
    {
        $norte = new Sala('Sala Norte', capacidadMaxima: 10);
        $idNorte = $this->salas->registrar($norte);
        $ocupada = $this->reservaGuardada($norte, 'Marta', '10:00', '11:00');
        $this->apuntar($norte, new PeticionReserva($idNorte, self::franja('10:00', '11:00'), 'Ana', new Capacidad(4)));
        $notificador = $this->createMock(NotificadorReservas::class);
        $notificador->expects($this->once())
            ->method('reservaConfirmada')
            ->with($this->callback(fn(Reserva $reserva) => $reserva->persona() === 'Ana'));

        $this->cancelar($notificador)->ejecutar($this->reservas->idDe($ocupada));

        self::assertSame(['Ana'], self::personas($this->reservas->confirmadasDeLaSala($norte)));
        self::assertSame([], $this->listas->deLaSala($norte)->porOrdenDeLlegada());
    }

    public function test_si_la_primera_peticion_no_se_admite_la_sala_es_para_la_siguiente(): void
    {
        $auditorio = new Sala('Auditorio', capacidadMaxima: 40);
        $idAuditorio = $this->salas->registrar($auditorio);
        foreach (['2027-02-25', '2027-02-26', '2027-02-27'] as $dia) {
            $tardia = new Reserva($auditorio, new FranjaHoraria(new \DateTimeImmutable("$dia 10:00"), new \DateTimeImmutable("$dia 11:00")), 'Ana', new Capacidad(4));
            $tardia->cancelarTarde();
            $this->reservas->guardar($tardia);
        }
        $ocupada = $this->reservaGuardada($auditorio, 'Marta', '10:00', '11:00');
        $ana = new PeticionReserva($idAuditorio, self::franja('10:00', '11:00'), 'Ana', new Capacidad(4));
        $this->apuntar($auditorio, $ana);
        $this->apuntar($auditorio, new PeticionReserva($idAuditorio, self::franja('10:00', '11:00'), 'Luis', new Capacidad(4)));

        $this->cancelar($this->createStub(NotificadorReservas::class))->ejecutar($this->reservas->idDe($ocupada));

        self::assertSame(['Luis'], self::personas($this->reservas->confirmadasDeLaSala($auditorio)));
        self::assertEquals([$ana], $this->listas->deLaSala($auditorio)->porOrdenDeLlegada());
    }

    public function test_una_peticion_cuya_franja_sigue_ocupada_por_otra_reserva_no_recibe_nada(): void
    {
        $norte = new Sala('Sala Norte', capacidadMaxima: 10);
        $idNorte = $this->salas->registrar($norte);
        $ocupada = $this->reservaGuardada($norte, 'Marta', '10:00', '11:00');
        $this->reservaGuardada($norte, 'Pablo', '11:00', '12:00');
        $ana = new PeticionReserva($idNorte, self::franja('10:00', '12:00'), 'Ana', new Capacidad(4));
        $this->apuntar($norte, $ana);
        $notificador = $this->createMock(NotificadorReservas::class);
        $notificador->expects($this->never())->method('reservaConfirmada');

        $this->cancelar($notificador)->ejecutar($this->reservas->idDe($ocupada));

        self::assertSame(['Pablo'], self::personas($this->reservas->confirmadasDeLaSala($norte)));
        self::assertEquals([$ana], $this->listas->deLaSala($norte)->porOrdenDeLlegada());
    }

    private function cancelar(NotificadorReservas $notificador): CancelarReserva
    {
        $reloj = new MockClock(new \DateTimeImmutable('2027-03-01 09:00'));

        return new CancelarReserva(
            $this->reservas,
            $reloj,
            $this->listas,
            new ReservarSala($this->salas, $this->reservas, $notificador, new ReglaDePenalizacion($this->reservas, $reloj)),
        );
    }

    private function reservaGuardada(Sala $sala, string $persona, string $inicio, string $fin): Reserva
    {
        $reserva = new Reserva($sala, self::franja($inicio, $fin), $persona, new Capacidad(4));
        $this->reservas->guardar($reserva);

        return $reserva;
    }

    private function apuntar(Sala $sala, PeticionReserva $peticion): void
    {
        $lista = $this->listas->deLaSala($sala);
        $lista->apuntar($peticion);
        $this->listas->guardar($sala, $lista);
    }

    /**
     * @param list<Reserva> $reservas
     *
     * @return list<string>
     */
    private static function personas(array $reservas): array
    {
        return array_map(fn(Reserva $reserva) => $reserva->persona(), $reservas);
    }

    private static function franja(string $inicio, string $fin): FranjaHoraria
    {
        return new FranjaHoraria(new \DateTimeImmutable("2027-03-04 $inicio"), new \DateTimeImmutable("2027-03-04 $fin"));
    }
}
