<?php

namespace Fastbolt\WorkingDayProvider;

use Webmozart\Assert\Assert;

class Configuration
{
    /**
     * Array of week days to exclude from working days as per ISO-8601 / `date('N')`.
     * Defaults to Saturday and Sunday (6 and 7).
     *
     * @var int[]
     */
    private array $excludeWeekDays = [6, 7];

    /**
     * @param array{excludeWeekDays?: int[]} $config
     */
    public function __construct(array $config = [])
    {
        Assert::allInteger($config['excludeWeekDays'] ?? [], 'excludeWeekDays must be an array of integers');
        Assert::allGreaterThanEq(
            $config['excludeWeekDays'] ?? [],
            1,
            'excludeWeekDays must be an array of integers between 1 and 7'
        );
        Assert::allLessThanEq(
            $config['excludeWeekDays'] ?? [],
            7,
            'excludeWeekDays must be an array of integers between 1 and 7'
        );

        $this->excludeWeekDays = $config['excludeWeekDays'] ?? $this->excludeWeekDays;
    }

    /**
     * @return self
     */
    public static function getDefaultConfiguration(): self
    {
        return new self();
    }

    /**
     * @return int[]
     */
    public function getExcludeWeekDays(): array
    {
        return $this->excludeWeekDays;
    }
}
