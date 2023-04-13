<?php

namespace Fastbolt\WorkingDayProvider\Tests\_Fixtures\Holiday;

use DateTimeImmutable;
use DateTimeInterface;
use Fastbolt\WorkingDayProvider\Holiday\Holiday;

class HolidayImplementation implements Holiday
{
    private DateTimeInterface $date;

    public function __construct(string $date)
    {
        $this->date = new DateTimeImmutable($date);
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
