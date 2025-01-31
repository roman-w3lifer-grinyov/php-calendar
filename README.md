# [php-calendar](https://packagist.org/packages/w3lifer/php-calendar)

- [Installation](#installation)
- [Usage](#usage)
  - [Default configuration](#default-configuration)
  - [Method `get()`](#method-get)
  - [Example of HTML calendar](#example-of-html-calendar)
  - [Example of 12-month calendar](#example-of-12-month-calendar)
  - [Example of periodic calendar](#example-of-periodic-calendar)
- [Tests](#tests)

## Installation

``` sh
composer require w3lifer/php-calendar
```

## Usage

### Default configuration

> Can be changed as you wish

``` php
$phpCalendar = new PhpCalendar([
    'language' => 'en',
    'monthNames' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    'weekDayAbbrs' => ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
    'firstDayOfWeek' => 1,
    'timezone' => date_default_timezone_get(),
]);
```

### Method `get()`

This method is the main public method in this class:

``` php
public function get(
    int $numberOfMonths = 12,
    int $startMonth = null, // Default: date('n')
    int $startYear = null // Default: date('Y')
): PhpCalendar
```

It returns a `PhpCalendar` object with the following public properties:

- `$html string` — calendar in HTML format
- `$prevPeriodParams array` — parameters for the previous period
- `$nextPeriodParams array` — parameters for the next period

The following period parameters are available (array keys):

- `numberOfMonth`
- `startMonth`
- `startYear`

> The `PhpCalendar` class also defines a `__toString()` method that simply returns its own `$html` property (HTML calendar)

### Example of HTML calendar

``` html
<div class="php-calendar" data-timezone="UTC" data-timezone-offset="0">
    <div class="php-calendar_month-box" data-month-number="1">
        <table>
            <caption>
                <span class="php-calendar_month-name">January</span>
                <span class="php-calendar_year-name">1970</span>
            </caption>
            <tr>
                <th>Mon<th>Tue<th>Wed<th>Thu<th>Fri<th>Sat<th>Sun
            <tr>
                <td data-week-number="1" data-day-number="363" data-timestamp="-259200" class="php-calendar_day_other-month">
                    <span class="php-calendar_day">29</span>
                <td data-week-number="1" data-day-number="364" data-timestamp="-172800" class="php-calendar_day_other-month">
                    <span class="php-calendar_day">30</span>
                <td data-week-number="1" data-day-number="365" data-timestamp="-86400" class="php-calendar_day_other-month">
                    <span class="php-calendar_day">31</span>
                <td data-week-number="1" data-day-number="1" data-timestamp="0">
                    <span class="php-calendar_day php-calendar_today">1</span>
                <td data-week-number="1" data-day-number="2" data-timestamp="86400">
                    <span class="php-calendar_day">2</span>
                <td data-week-number="1" data-day-number="3" data-timestamp="172800">
                    <span class="php-calendar_day">3</span>
                <td data-week-number="1" data-day-number="4" data-timestamp="259200">
                    <span class="php-calendar_day">4</span>
            ...
        </table>
    </div>
    ...
</div>
```

### Example of 12-month calendar

``` php
<?php

$year = $_GET['year'] ?? null;
$phpCalendar = (new PhpCalendar())->get(12, 1, $year); // ->getYear($year);
$prevSearchParams = '?year=' . $phpCalendar->prevPeriodParams['startYear'];
$nextSearchParams = '?year=' . $phpCalendar->nextPeriodParams['startYear'];

?>

<style>body {text-align: center;} table {margin: auto;}</style>
<hr>
<a href="<?= $prevSearchParams ?>">«««</a> <a href="<?= $nextSearchParams ?>">»»»</a>
<hr>

<?= $phpCalendar ?>
```

### Example of periodic calendar

``` php
<?php

$numberOfMonths = $_GET['number-of-months'] ?? 6;
// Set `$startMonth` to `null` to get an auto-scrolling calendar
$startMonth = $_GET['start-month'] ?? 1;
$startYear = $_GET['start-year'] ?? null;
$phpCalendar = (new PhpCalendar())->get($numberOfMonths, $startMonth, $startYear);
$prevSearchParams =
    '?number-of-months=' . $numberOfMonths .
    '&start-month=' . $phpCalendar->prevPeriodParams['startMonth'] .
    '&start-year=' . $phpCalendar->prevPeriodParams['startYear'];
$nextSearchParams =
    '?number-of-months=' . $numberOfMonths .
    '&start-month=' . $phpCalendar->nextPeriodParams['startMonth'] .
    '&start-year=' . $phpCalendar->nextPeriodParams['startYear'];
?>

<style>body {text-align: center;} table {margin: auto;}</style>
<hr>
<a href="<?= $prevSearchParams ?>">«««</a> <a href="<?= $nextSearchParams ?>">»»»</a>
<hr>

<?= $phpCalendar ?>
```

## Tests

``` sh
make tests
```
