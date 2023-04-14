[![Composer version](https://img.shields.io/packagist/v/fastbolt/working-day-provider)](https://packagist.org/packages/fastbolt/working-day-provider)

[![Code Climate maintainability](https://img.shields.io/codeclimate/maintainability/fastbolt/working-day-provider)](https://codeclimate.com/github/fastbolt/working-day-provider)
[![Test Coverage](https://img.shields.io/codecov/c/github/fastbolt/working-day-provider)](https://app.codecov.io/gh/fastbolt/working-day-provider/)

[![Type Coverage](https://shepherd.dev/github/fastbolt/working-day-provider/coverage.svg)](https://shepherd.dev/github/fastbolt/working-day-provider)
[![Psalm Level](https://shepherd.dev/github/fastbolt/working-day-provider/level.svg)](https://shepherd.dev/github/fastbolt/working-day-provider)

[![Github Build](https://img.shields.io/github/actions/workflow/status/fastbolt/working-day-provider/phpunit.yaml?branch=main)](https://github.com/fastbolt/working-day-provider/actions)

# Working day provider

PHP package to ease calculation of number of working days between two given dates.

## Prerequisites

For now, the bundle is tested using PHP 7.4, 8.0 and 8.1.

## Installation

The library can be installed via composer:

```
composer require fastbolt/working-day-provider
```

## Usage

### Basic usage

For the most basic usage, you can use the `WorkingDayProvider` without any configuration.

By default, it will not use any application or region specific holiday, but only consider monday to friday as working
days.

```php
use Fastbolt\WorkingDayProvider\WorkingDayProvider;

$workingDayProvider = new WorkingDayProvider();
$workingDays = $workingDayProvider->getWorkingDaysForPeriod(
    new DateTimeImmutable('2023-01-01'),
    new DateTimeImmutable('2023-01-07')
);

// $workingDays will be 5
```

### Application / region specific holidays

The library is designed to include application / region specific holidays as "non-working days".

To do so, you need to create a class implementing the `Fastbolt\WorkingDayProvider\Holiday\HolidayProvider` interface.

The only interface method `getHolidaysForDateRange` must return an array of objects implementing
the `\Fastbolt\WorkingDayProvider\Holiday\Holiday` interface.

```php
use Acme\Provider\HolidayProvider;
use Fastbolt\WorkingDayProvider\WorkingDayProvider;

$holidayProvider = new HolidayProvider());

# Example `$holidayProvider->getHolidaysForDateRange(2022-12-24, 2022-12-26)` returns one holiday for 26th of december.
$workingDayProvider = new WorkingDayProvider($holidayProvider);
$workingDays = $workingDayProvider->getWorkingDaysForPeriod(
    new DateTimeImmutable('2022-12-24'),
    new DateTimeImmutable('2022-12-26')
);

// $workingDays will be 0 (days are saturday, sunday and holiday)
```

### Custom configuration (not yet fully-implemented)

Optionally, you can provide a custom configuration to the `WorkingDayProvider` constructor.

```php
use Fastbolt\WorkingDayProvider\Configuration;
use Fastbolt\WorkingDayProvider\WorkingDayProvider;

$configuration = new Configuration(['excludeWeekDays' => [1, 6, 7]);
$workingDayProvider = new WorkingDayProvider(null, $configuration);
$workingDays = $workingDayProvider->getWorkingDaysForPeriod(
    new DateTimeImmutable('2022-12-24'),
    new DateTimeImmutable('2022-12-26')
);

// $workingDays will be 0 (working days monday, saturday and sunday are all configured as non-working days)
```

