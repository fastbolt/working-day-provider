<?php

namespace Fastbolt\WorkingDayProvider\Holiday;

use DateTimeInterface;

interface Holiday
{
    public function getDate(): DateTimeInterface;
}
