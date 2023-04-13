<?php

namespace Fastbolt\WorkingDayProvider\Holiday;

use DateTimeInterface;

interface Holiday
{
    /**
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface;
}
