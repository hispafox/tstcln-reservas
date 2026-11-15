<?php

declare(strict_types=1);

namespace App\Tests\Arranque;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class AplicacionArrancaTest extends KernelTestCase
{
    public function testElKernelArrancaYConectaConSqlite(): void
    {
        self::bootKernel();

        $conexion = self::getContainer()->get(Connection::class);

        self::assertSame(1, (int) $conexion->fetchOne('SELECT 1'));
        self::assertInstanceOf(SQLitePlatform::class, $conexion->getDatabasePlatform());
    }
}
