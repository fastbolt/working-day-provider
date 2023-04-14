<?php

namespace Fastbolt\WorkingDayProvider\Tests;

use Fastbolt\TestHelpers\BaseTestCase;
use Fastbolt\WorkingDayProvider\Configuration;
use InvalidArgumentException;

/**
 * @covers \Fastbolt\WorkingDayProvider\Configuration
 */
class ConfigurationTest extends BaseTestCase
{
    public function testGetDefaultConfiguration()
    {
        $config = Configuration::getDefaultConfiguration();
        self::assertSame([6, 7], $config->getExcludeWeekDays());
    }

    public function testConstructDefaultValues(): void
    {
        $config = new Configuration();

        self::assertSame([6, 7], $config->getExcludeWeekDays());
    }

    public function testConstructCustomValues(): void
    {
        $config = new Configuration(['excludeWeekDays' => [1, 2, 3, 4, 5]]);
        self::assertSame([1, 2, 3, 4, 5], $config->getExcludeWeekDays());
    }

    public function testConstructWeekdayTooHigh(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Configuration(['excludeWeekDays' => [9]]);
    }

    public function testConstructWeekdayTooLow(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Configuration(['excludeWeekDays' => [0]]);
    }

    public function testConstructWeekdayNoInt(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Configuration(['excludeWeekDays' => ['Monday']]);
    }
}
