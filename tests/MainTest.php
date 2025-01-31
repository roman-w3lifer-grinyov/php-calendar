<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use w3lifer\PhpCalendar\PhpCalendar;

final class MainTest extends TestCase
{
    public function testOutput(): void
    {
        $this->assertStringNotEqualsFile('tests/calendar-1970.html', (string) (new PhpCalendar())->get(1, 1, 1970));
        $this->assertStringEqualsFile('tests/calendar-1970.html', (string) (new PhpCalendar())->get(12, 1, 1970));
        $this->assertStringEqualsFile('tests/calendar-1970.html', (string) (new PhpCalendar())->getYear(1970));

        $this->assertStringNotEqualsFile('tests/calendar-2020.html', (string) (new PhpCalendar())->get(1, 1, 2020));
        $this->assertStringEqualsFile('tests/calendar-2020.html', (string) (new PhpCalendar())->get(12, 1, 2020));
        $this->assertStringEqualsFile('tests/calendar-2020.html', (string) (new PhpCalendar())->getYear(2020));
    }

    public function testPrevPeriodParams(): void
    {
        $this->assertNotEquals(['numberOfMonth' => 1, 'startMonth' => 1, 'startYear' => 1970], (new PhpCalendar())->get(1, 1, 1970)->prevPeriodParams);
        $this->assertEquals(['numberOfMonth' => 1, 'startMonth' => 12, 'startYear' => 1969], (new PhpCalendar())->get(1, 1, 1970)->prevPeriodParams);
    }

    public function testNextPeriodParams(): void
    {
        $this->assertNotEquals(['numberOfMonth' => 1, 'startMonth' => 1, 'startYear' => 1970], (new PhpCalendar())->get(1, 1, 1970)->nextPeriodParams);
        $this->assertEquals(['numberOfMonth' => 1, 'startMonth' => 2, 'startYear' => 1970], (new PhpCalendar())->get(1, 1, 1970)->nextPeriodParams);
    }
}
