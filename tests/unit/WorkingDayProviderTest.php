<?php

namespace Fastbolt\WorkingDayProvider\Tests;

use DateTime;
use DateTimeInterface;
use Fastbolt\TestHelpers\BaseTestCase;
use Fastbolt\WorkingDayProvider\Configuration;
use Fastbolt\WorkingDayProvider\Holiday\HolidayProvider;
use Fastbolt\WorkingDayProvider\Tests\_Fixtures\Holiday\HolidayImplementation;
use Fastbolt\WorkingDayProvider\WorkingDayProvider;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \Fastbolt\WorkingDayProvider\WorkingDayProvider
 */
class WorkingDayProviderTest extends BaseTestCase
{
    /**
     * @var HolidayProvider&MockObject
     */
    private $holidayProvider;

    /**
     * @var Configuration&MockObject
     */
    private $configuration;

    /**
     * @dataProvider getWorkingDaysForPeriodDataProvider
     */
    public function testGetWorkingDaysForPeriod(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        array $holidays,
        int $expectedWorkingDayCount
    ) {
        $this->configuration->method('getExcludeWeekDays')
                            ->willReturn([6, 7]);
        $this->holidayProvider->method('getHolidaysForDateRange')
                              ->with($startDate, $endDate)
                              ->willReturn($holidays);

        $provider = new WorkingDayProvider($this->configuration, $this->holidayProvider);
        $result   = $provider->getWorkingDaysForPeriod($startDate, $endDate);

        self::assertEquals($expectedWorkingDayCount, $result);
    }

    public function getWorkingDaysForPeriodDataProvider(): array
    {
        return [
            [
                new DateTime('2020-01-01 00:00:00'),
                new DateTime('2020-01-01 23:59:59'),
                [],
                1,
            ],
            [
                new DateTime('2020-12-13 00:00:00'),
                new DateTime('2020-12-13 23:59:59'),
                [],
                0,
            ],
            [
                new DateTime('2020-12-13 00:00:00'),
                new DateTime('2020-12-19 23:59:59'),
                [],
                5,
            ],
            [
                new DateTime('2020-12-10 00:00:00'),
                new DateTime('2020-12-15 23:59:59'),
                [],
                4,
            ],
            [
                new DateTime('2020-12-12 00:00:00'),
                new DateTime('2020-12-12 23:59:59'),
                [],
                0,
            ],
            [
                new DateTime('2020-02-28 00:00:00'),
                new DateTime('2020-03-02 23:59:59'),
                [],
                2,
            ],
            [
                new DateTime('2020-12-01 00:00:00'),
                new DateTime('2020-12-10 23:59:59'),
                [
                    new HolidayImplementation('2020-12-05'),
                    new HolidayImplementation('2020-12-08'),
                ],
                7,
            ],
            [
                new DateTime('2023-04-01'),
                new DateTime('2023-04-12'),
                [
                    new HolidayImplementation('2023-04-07'),
                    new HolidayImplementation('2023-04-10'),
                ],
                6,
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->holidayProvider = $this->getMock(HolidayProvider::class);
        $this->configuration   = $this->getMock(Configuration::class);
    }
}
