<?php

namespace Fastbolt\WorkingDayProvider\Holiday;

use DateTimeInterface;

interface HolidayProvider
{
    /**
     * @param DateTimeInterface $periodStart
     * @param DateTimeInterface $periodEnd
     *
     * @return Holiday[]
     */
    public function getHolidaysForDateRange(DateTimeInterface $periodStart, DateTimeInterface $periodEnd): array;
}
