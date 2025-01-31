<?php

declare(strict_types=1);

namespace w3lifer\PhpCalendar;

use DateTime;
use DateTimeZone;
use Exception;

class PhpCalendar
{
    /*
     * =================================================================================================================
     * PRIVATE PROPERTIES
     * =================================================================================================================
     */

    /**
     * Translations
     */
    private array $i18n;

    /**
     * Two-letter lowercase language code according to ISO-639-1
     * @see https://loc.gov/standards/iso639-2/php/code_list.php
     */
    private string $language = 'en';

    /**
     * Month names: 12 elements
     */
    private array $monthNames;

    /**
     * Abbreviations of the days of the week: 7 elements
     */
    private array $weekDayAbbrs;

    /**
     * First day of the week: 1 (Monday) - 7 (Sunday)
     * We can set its value to be the name of the day of the week in English or an English abbreviation of the day of
     * the week (three letters) via the configuration array passed to the constructor
     * For example:
     * ```
     * $phpCalendar = new PhpCalendar(['firstDayOfWeek' => 7]);
     * $phpCalendar = new PhpCalendar(['firstDayOfWeek' => 'Sun']);
     * $phpCalendar = new PhpCalendar(['firstDayOfWeek' => 'Sunday']);
     * $phpCalendar = new PhpCalendar(['firstDayOfWeek' => 'sun']);
     * $phpCalendar = new PhpCalendar(['firstDayOfWeek' => 'sunday']);
     * ```
     */
    private int|string $firstDayOfWeek = 1;

    /**
     * New timezone
     */
    private string $newTimezone = '';

    /**
     * The timezone of the context where methods of this class will be called
     */
    private string $oldTimezone = '';

    /**
     * Current Unix timestamp
     */
    private int $today;

    /*
     * =================================================================================================================
     * PUBLIC PROPERTIES
     * =================================================================================================================
     */

    /**
     * Resulting markup
     */
    public string $html = '';

    public array $prevPeriodParams = [];
    public array $nextPeriodParams = [];

    /*
     * =================================================================================================================
     * METHODS
     * =================================================================================================================
     */

    public function __toString(): string
    {
        return $this->html;
    }

    /**
     * ```
     * $config['language']
     * $config['monthNames']
     * $config['weekDayAbbrs']
     * $config['firstDayOfWeek']
     * $config['timezone']
     * ```
     * @see $language
     * @see $monthNames
     * @see $weekDayAbbrs
     * @see $firstDayOfWeek
     * @see $newTimezone
     */
    public function __construct(array $config = [])
    {
        $this->i18n = PhpCalendarI18n::getAll();

        // Language

        if (isset($config['language'])) {
            if (array_key_exists($config['language'], $this->i18n['monthNames'])) {
                $this->language = $config['language'];
            }
            unset($config['language']);
        }

        // Month names

        if (isset($config['monthNames'])) {
            $this->setMonthsNames($config['monthNames']);
            unset($config['monthNames']);
        } else {
            $this->monthNames = $this->i18n['monthNames'][$this->language];
        }

        // Abbreviations of the days of the week

        if (isset($config['weekDayAbbrs'])) {
            $this->setWeekDayAbbrs($config['weekDayAbbrs']);
            unset($config['weekDayAbbrs']);
        } elseif ($this->language == 'ru') {
            $this->weekDayAbbrs = $this->i18n['weekDayAbbrs2'][$this->language];
        } else {
            $this->weekDayAbbrs = $this->i18n['weekDayAbbrs3'][$this->language];
        }

        // First day of the week

        if (isset($config['firstDayOfWeek'])) {
            if (is_int($config['firstDayOfWeek'])) {
                $this->setFirstDayOfWeek_ByWeekDayNumber($config['firstDayOfWeek']);
            } elseif (is_string($config['firstDayOfWeek'])) {
                $this->setFirstDayOfWeek_ByWeekDayString($config['firstDayOfWeek']);
            } else {
                throw new Exception('Type of the "firstDayOfWeek" element must be either integer or string');
            }
            unset($config['firstDayOfWeek']);
        }

        // Timezone

        if (isset($config['timezone'])) {
            $this->newTimezone = $config['timezone'];
            unset($config['timezone']);
        }

        // Check unknown properties

        if ($config) {
            throw new Exception('Setting unknown property: ' . static::class . '::$' . array_keys($config)[0]);
        }
    }

    /**
     * @param array $monthNames January (0) - December (11)
     */
    private function setMonthsNames(array $monthNames): void
    {
        if (count($monthNames) !== 12) {
            throw new Exception('The number of month names should be 12');
        }
        $this->monthNames = $monthNames;
    }

    /**
     * @param array $weekDayAbbrs Monday (0) - Sunday (6)
     */
    private function setWeekDayAbbrs(array $weekDayAbbrs): void
    {
        if (count($weekDayAbbrs) !== 7) {
            throw new Exception('The number of abbreviations of the days of the week should be 7');
        }
        $this->weekDayAbbrs = $weekDayAbbrs;
    }

    /**
     * @param int $weekDayNumber Week day number (1-7)
     */
    private function setFirstDayOfWeek_ByWeekDayNumber(int $weekDayNumber): void
    {
        if ($weekDayNumber < 1 || $weekDayNumber > 7) {
            throw new Exception('The number of days of the week must be between 1 and 7 inclusively');
        }
        $this->firstDayOfWeek = $weekDayNumber;
        $this->shiftWeekDayAbbrs();
    }

    /**
     * @param string $weekDayString Mon-Sun, Monday-Sunday, mon-sun, monday-sunday
     */
    private function setFirstDayOfWeek_ByWeekDayString(string $weekDayString): void
    {
        // Mon-Sun
        $key = array_search($weekDayString, $this->i18n['weekDayAbbrs3']['en']);
        // Monday-Sunday
        if ($key === false) { // [!] Strict comparison with `false`
            $key = array_search($weekDayString, $this->i18n['weekDayNames']['en']);
            // mon-sun
            if ($key === false) {
                $key = array_search($weekDayString, $this->i18n['weekDayAbbrs3']['en']);
                // monday-sunday
                if ($key === false) {
                    $key = array_search($weekDayString, $this->i18n['weekDayNames']['en']);
                }
            }
        }
        if ($key === false) {
            throw new Exception('The name of the day of the week must be: Mon-Sun, Monday-Sunday, mon-sun or monday-sunday');
        }
        $this->firstDayOfWeek = ++$key;
        $this->shiftWeekDayAbbrs();
    }

    /**
     * Shifts the abbreviations of the days of the week by the number of the first day of the week
     * @see setFirstDayOfWeek_ByWeekDayNumber()
     * @see setFirstDayOfWeek_ByWeekDayString()
     */
    private function shiftWeekDayAbbrs(): void
    {
        for ($i = 1; $i < $this->firstDayOfWeek; $i++) {
            $this->weekDayAbbrs[] = array_shift($this->weekDayAbbrs);
        }
    }

    public function get(int $numberOfMonths, int $startMonth = null, int $startYear = null): PhpCalendar
    {
        $this->setTimezone();

        $this->setToday();

        $this->validateNumberOfMonths($numberOfMonths);

        if ($startMonth === null) {
            $startMonth = (int) date('n');
        } else {
            $this->validateStartMonthNumber($startMonth);
        }

        if ($startYear === null) {
            $startYear = (int) date('Y');
        }

        $prevMonth = $nextMonth = $startMonth;
        $prevYear = $nextYear = $startYear;

        $markup = '';
        for ($i = 1; $i <= $numberOfMonths; $i++) {
            $markup .= $this->getMonthMarkup($nextYear, $nextMonth);
            $nextMonth++;
            if ($nextMonth === 13) {
                $nextMonth = 1;
                $nextYear++;
            }
            $prevMonth--;
            if ($prevMonth === 0) {
                $prevMonth = 12;
                $prevYear--;
            }
        }
        $markup = $this->wrap($markup);

        $this->html = $markup;

        // Search parameters for the previous period

        $this->prevPeriodParams['numberOfMonth'] = $numberOfMonths;
        $this->prevPeriodParams['startMonth'] = $prevMonth;
        $this->prevPeriodParams['startYear'] = $prevYear;

        // Search parameters for the next period

        $this->nextPeriodParams['numberOfMonth'] = $numberOfMonths;
        $this->nextPeriodParams['startMonth'] = $nextMonth;
        $this->nextPeriodParams['startYear'] = $nextYear;

        $this->resetTimezone();

        return $this;
    }

    private function setTimezone(): void
    {
        if (!$this->newTimezone) {
            return;
        }
        $this->oldTimezone = date_default_timezone_get();
        if (date_default_timezone_set($this->newTimezone)) {
            return;
        }
        throw new Exception('Can not set the timezone');
    }

    /**
     * Sets the current Unix timestamp to the `$today` property
     */
    private function setToday(): void
    {
        $this->today = mktime(0, 0, 0, (int) date('n'), (int) date('j'), (int) date('Y'));
    }

    private function validateNumberOfMonths(int $numberOfMonths): void
    {
        if ($numberOfMonths <= 0) {
            throw new Exception('The number of months must be greater than 0');
        }
    }

    private function validateStartMonthNumber(int $monthNumber): void
    {
        if ($monthNumber < 1 || $monthNumber > 12) {
            throw new Exception('The month number must be between 1 and 12 inclusively');
        }
    }

    /**
     * Returns the table of  month as an HTML string
     */
    private function getMonthMarkup(int $year, int $monthNumber): string
    {
        $matrix = $this->getMonthMatrix($year, $monthNumber);

        $monthName = $this->monthNames[$monthNumber - 1];

        $table =
            '<div class="php-calendar_month-box" data-month-number="' . $monthNumber . '">' .
                '<table>' .
                    '<caption>' .
                        '<span class="php-calendar_month-name">' . $monthName . '</span>' .
                        ' ' .
                        '<span class="php-calendar_year-name">' . $year . '</span>' .
                    '</caption>' .
                    '<tr>' .
                        '<th>' . implode('<th>', $this->weekDayAbbrs) .
                    '<tr>';

        $cellsNumber = count($matrix);

        for ($i = 0; $i < $cellsNumber; $i++) {
            // Day
            $day = date('j', $matrix[$i]);
            // Today
            $today = $matrix[$i] === $this->today ? ' php-calendar_today' : '';
            $day = '<span class="php-calendar_day' . $today . '">' . $day . '</span>';
            // <td>
            $td = '<td';
            $td .= ' data-week-number="' . (int) date('W', $matrix[$i]) . '"';
            $td .= ' data-day-number="' . ((int) date('z', $matrix[$i]) + 1) . '"';
            $td .= ' data-timestamp="' . $matrix[$i] . '"';
            if (($i + 1) % 7 === 0 && ($i !== $cellsNumber - 1)) {
                $table .= $td . '>' . $day . '<tr>';
            } else {
                $td .= (int) date('n', $matrix[$i]) !== $monthNumber ? ' class="php-calendar_day_other-month">' : '>';
                $table .= $td . $day;
            }
        }

        $table .= '</table></div>';

        return $table;
    }

    /**
     * Returns an array of Unix timestamps representing the days of the month
     */
    private function getMonthMatrix(int $year, int $month): array
    {
        // Initial data

        $firstDayTimestamp = mktime(0, 0, 0, $month, 1, $year);

        $matrix = [];

        // First "row"

        $interval = date('N', $firstDayTimestamp) - $this->firstDayOfWeek;
        if ($interval < 0) {
            $interval += 7;
        }
        for ($i = 0; $i > -$interval; $i--) {
            $matrix[] = mktime(0, 0, 0, $month, $i, $year);
        }
        $matrix = array_reverse($matrix);

        // Days

        $daysInMonth = date('t', $firstDayTimestamp);
        $matrix[] = $firstDayTimestamp;
        for ($i = 2; $i <= $daysInMonth; $i++) {
            $matrix[] = mktime(0, 0, 0, $month, $i, $year);
        }

        // Last "row"

        $nextMonth = $month + 1;
        $interval = $this->firstDayOfWeek - date('N', mktime(0, 0, 0, $nextMonth, 1, $year));
        if ($interval < 0) {
            $interval += 7;
        }
        for ($i = 1; $i <= $interval; $i++) {
            $matrix[] = mktime(0, 0, 0, $nextMonth, $i, $year);
        }

        // Return

        return $matrix;
    }

    /**
     * Wraps the calendar with markup and sets the values for the `data-timezone` and `data-timezone-offset` attributes
     */
    private function wrap(string $calendar): string
    {
        return '<div' .
            ' class="php-calendar"' .
            ' data-timezone="' . date_default_timezone_get() . '"' .
            ' data-timezone-offset="' . $this->getTimezoneOffset() . '"' .
        '>' . $calendar . '</div>';
    }

    private function getTimezoneOffset(): int
    {
        $timezoneObj = new DateTimeZone(date_default_timezone_get());
        return $timezoneObj->getOffset(new DateTime('now', $timezoneObj));
    }

    /**
     * Resets the timezone to the previous value
     */
    private function resetTimezone(): void
    {
        if ($this->oldTimezone) {
            date_default_timezone_set($this->oldTimezone);
        }
    }

    /*
     * =================================================================================================================
     * HELPERS
     * =================================================================================================================
     */

    /**
     * Returns the calendar for the specified year (default for the current year)
     */
    public function getYear(int $year = null): PhpCalendar
    {
        return $this->get(12, 1, $year);
    }
}
