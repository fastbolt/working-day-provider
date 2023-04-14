<?php

namespace Fastbolt\WorkingDayProvider;

use DateTimeInterface;
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
     * @param DateTimeInterface $periodStart
     * @param DateTimeInterface $periodEnd
     *
     * @return int
     */
    public function getWorkingDaysForPeriod(DateTimeInterface $periodStart, DateTimeInterface $periodEnd): int
    {
        $holidays  = null === $this->holidayProvider
            ? []
            : $this->holidayProvider->getHolidaysForDateRange($periodStart, $periodEnd);
        $endDate   = $periodEnd->getTimestamp();
        $startDate = $periodStart->getTimestamp();

        //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
        //We add one to inlude both dates in the interval.
        $days = ($endDate - $startDate) / 86400 + 1;

        $numFullWeeks     = floor($days / 7);
        $numRemainingDays = fmod($days, 7);

        //It will return 1 if it's Monday,.. ,7 for Sunday
        $firstDayOfWeek = (int)date('N', $startDate);
        $lastDayOfWeek  = (int)date('N', $endDate);

        //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
        //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
        if ($firstDayOfWeek <= $lastDayOfWeek) {
            if ($firstDayOfWeek <= 6 && 6 <= $lastDayOfWeek) {
                $numRemainingDays--;
            }
            if ($firstDayOfWeek <= 7 && 7 <= $lastDayOfWeek) {
                $numRemainingDays--;
            }
        } elseif ($firstDayOfWeek === 7) {
            // if the start date is a Sunday, then we definitely subtract 1 day
            $numRemainingDays--;

            if ($lastDayOfWeek === 6) {
                // if the end date is a Saturday, then we subtract another day
                $numRemainingDays--;
            }
        } else {
            // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
            // so we skip an entire weekend and subtract 2 days
            $numRemainingDays -= 2;
        }

        //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
        $workingDays = $numFullWeeks * 5;
        if ($numRemainingDays > 0) {
            $workingDays += $numRemainingDays;
        }

        //We subtract the holidays
        foreach ($holidays as $holiday) {
            $timestamp = strtotime($holiday->getDate()->format('Y-m-d'));
            //If the holiday doesn't fall in weekend
            if (
                $startDate <= $timestamp
                && $timestamp <= $endDate
                && !in_array((int)date('N', $timestamp), $this->configuration->getExcludeWeekDays(), true)
            ) {
                $workingDays--;
            }
        }

        return (int)$workingDays;
    }
}
