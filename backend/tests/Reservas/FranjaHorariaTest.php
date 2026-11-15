<?php

declare(strict_types=1);

namespace App\Tests\Reservas;

use App\Reservas\FranjaHoraria;
use PHPUnit\Framework\TestCase;

final class FranjaHorariaTest extends TestCase
{
    public function testFranja(): void
    {
        $diez = new \DateTimeImmutable('2027-03-04 10:00');
        $diezYMedia = new \DateTimeImmutable('2027-03-04 10:30');
        $once = new \DateTimeImmutable('2027-03-04 11:00');
        $onceYMedia = new \DateTimeImmutable('2027-03-04 11:30');
        $doce = new \DateTimeImmutable('2027-03-04 12:00');

        $franja = new FranjaHoraria($diez, $once);
        self::assertTrue($franja->seSolapaCon(new FranjaHoraria($diezYMedia, $onceYMedia)));
        self::assertFalse($franja->seSolapaCon(new FranjaHoraria($once, $doce)));

        $this->expectException(\InvalidArgumentException::class);
        new FranjaHoraria($once, $diez);
    }
}
