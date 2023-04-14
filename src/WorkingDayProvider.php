<?php

namespace Fastbolt\WorkingDayProvider;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Fastbolt\WorkingDayProvider\Holiday\Holiday;
use Fastbolt\WorkingDayProvider\Holiday\HolidayProvider;

class WorkingDayProvider
{
    private Configuration $configuration;

    private ?HolidayProvider $holidayProvider;

    /**
     * @param HolidayProvider|null $holidayProvider
     * @param Configuration|null   $configuration
     */
    public function __construct(?HolidayProvider $holidayProvider = null, ?Configuration $configuration = null)
    {
        $this->holidayProvider = $holidayProvider;
        $this->configuration   = $configuration ?? Configuration::getDefaultConfiguration();
    }

    /**
     * @param DateTime $periodStart
     * @param DateTime $periodEnd
     *
     * @return int
     */
    public function getWorkingDaysForPeriod(
        DateTime $periodStart,
        DateTime $periodEnd
    ): int {
        $holidays = $this->getHolidays($periodStart, $periodEnd);

        // set time to 00:00:00
        $startDate = DateTimeImmutable::createFromMutable($periodStart)
                                      ->setTime(0, 0, 0);

        // set time to next day 00:00:00, otherwise end date is not included
        $endDate = DateTimeImmutable::createFromMutable($periodEnd)
                                    ->modify('+1 day')
                                    ->setTime(0, 0, 0);

        $oneDayInterval = DateInterval::createFromDateString('1 day');
        $oneDayPeriod   = new DatePeriod($startDate, $oneDayInterval, $endDate);
        $numDays        = 0;

        foreach ($oneDayPeriod as $iteratorDate) {
            if ($this->isExcludedDate($iteratorDate)) {
                continue;
            }

            if ($this->isHoliday($iteratorDate, $holidays)) {
                continue;
            }

            $numDays++;
        }

        return $numDays;
    }

    /**
     * @param DateTime $periodStart
     * @param DateTime $periodEnd
     *
     * @return Holiday[]
     */
    private function getHolidays(DateTime $periodStart, DateTime $periodEnd): array
    {
        return null === $this->holidayProvider
            ? []
            : $this->indexHolidays($this->holidayProvider->getHolidaysForDateRange($periodStart, $periodEnd));
    }

    /**
     * Helper method to index holidays by date in format `Y-m-d`
     *
     * @param Holiday[] $holidays
     *
     * @return array<string, Holiday>
     */
    private function indexHolidays(array $holidays): array
    {
        $result = [];
        foreach ($holidays as $holiday) {
            $result[$holiday->getDate()->format('Y-m-d')] = $holiday;
        }

        return $result;
    }

    /**
     * @param DateTimeInterface $iteratorDate
     *
     * @return bool
     */
    private function isExcludedDate(DateTimeInterface $iteratorDate): bool
    {
        $dayOfWeek = (int)$iteratorDate->format('N');

        // exclude week days from configuration object
        return in_array($dayOfWeek, $this->configuration->getExcludeWeekDays(), true);
    }

    /**
     * @param DateTimeInterface     $iteratorDate
     * @param array<string,Holiday> $holidays Array of holidays indexed by date in format `Y-m-d`
     *
     * @return bool
     */
    private function isHoliday(DateTimeInterface $iteratorDate, array $holidays): bool
    {
        $dateFormatted = $iteratorDate->format('Y-m-d');

        return array_key_exists($dateFormatted, $holidays);
    }
}
