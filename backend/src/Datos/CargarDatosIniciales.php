<?php

declare(strict_types=1);

namespace App\Datos;

use App\Reservas\FranjaHoraria;
use App\Reservas\Reserva;
use App\Reservas\Sala;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Vacía la base de datos y la deja con salas y reservas de ejemplo, para ver la aplicación con datos.
 * Las fechas se cuentan desde el día en que se lanza, en días laborables y dentro del horario de 8:00 a 20:00.
 */
#[AsCommand(
    name: 'app:cargar-datos-iniciales',
    description: 'Vacía la base de datos y carga salas y reservas de ejemplo',
)]
final class CargarDatosIniciales extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->recrearTablas();

        $norte = new Sala('Sala Norte', 8, equipamiento: ['pantalla']);
        $sur = new Sala('Sala Sur', 6);
        $grande = new Sala('Sala Grande', 20, 8, ['pantalla', 'videoconferencia']);
        $mirador = new Sala('Sala Mirador', 12, equipamiento: ['videoconferencia']);
        $cabina = new Sala('Cabina Este', 3);
        $salas = [$norte, $sur, $grande, $mirador, $cabina];

        $reservas = [
            $this->reserva($norte, 1, '09:00', '10:00', 'Ana', 5),
            $this->reserva($grande, 2, '11:00', '12:30', 'Ana', 12),
            $this->reserva($mirador, 4, '16:00', '17:00', 'Ana', 8),
            $this->reserva($sur, -1, '10:00', '11:00', 'Ana', 4),
            $this->reserva($norte, 3, '12:00', '13:00', 'Ana', 6),
            $this->reserva($norte, 1, '10:00', '12:00', 'Luis', 6),
            $this->reserva($grande, 1, '09:00', '11:00', 'Luis', 15),
            $this->reserva($sur, 1, '13:00', '14:30', 'Marta', 3),
            $this->reserva($mirador, 2, '09:00', '10:30', 'Marta', 10),
            $this->reserva($grande, 3, '15:00', '17:00', 'Jorge', 18),
            $this->reserva($norte, 2, '16:00', '18:00', 'Jorge', 7),
            $this->reserva($sur, 4, '09:00', '11:00', 'Lucia', 5),
        ];
        $reservas[4]->cancelar(false);

        foreach ([...$salas, ...$reservas] as $entidad) {
            $this->entityManager->persist($entidad);
        }
        $this->entityManager->flush();

        (new SymfonyStyle($input, $output))->success(
            sprintf('Cargadas %d salas y %d reservas.', count($salas), count($reservas)),
        );

        return Command::SUCCESS;
    }

    private function recrearTablas(): void
    {
        $metadatos = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $esquema = new SchemaTool($this->entityManager);
        $esquema->dropSchema($metadatos);
        $esquema->createSchema($metadatos);
        $this->entityManager->clear();
    }

    /**
     * @param int $dias días laborables desde hoy: 1 es el siguiente, -1 el anterior
     */
    private function reserva(Sala $sala, int $dias, string $inicio, string $fin, string $persona, int $asistentes): Reserva
    {
        $dia = self::diaLaborable($dias);

        return new Reserva(
            $sala,
            new FranjaHoraria(new \DateTimeImmutable($dia . ' ' . $inicio), new \DateTimeImmutable($dia . ' ' . $fin)),
            $persona,
            $asistentes,
        );
    }

    private static function diaLaborable(int $dias): string
    {
        $paso = $dias > 0 ? '+1 day' : '-1 day';
        $dia = new \DateTimeImmutable('today');
        for ($quedan = abs($dias); $quedan > 0;) {
            $dia = $dia->modify($paso);
            if ($dia->format('N') < 6) {
                --$quedan;
            }
        }

        return $dia->format('Y-m-d');
    }
}
