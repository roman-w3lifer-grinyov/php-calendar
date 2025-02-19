<?php

declare(strict_types=1);

namespace w3lifer\PhpCalendar;

use JetBrains\PhpStorm\ArrayShape;

class PhpCalendarI18n
{
    protected static array $monthNames = [
        'be' => ['Студзень', 'Люты', 'Сакавік', 'Красавік', 'Травень (Май)', 'Чэрвень', 'Ліпень', 'Жнівень', 'Верасень', 'Кастрычнік', 'Лістапад', 'Снежань'],
        'en' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        'ru' => ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
    ];

    protected static array $weekDayNames = [
        'be' => ['Панядзелак', 'Аўторак', 'Серада', 'Чацвер', 'Пятніца', 'Субота', 'Нядзеля'],
        'en' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
        'ru' => ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'],
    ];

    protected static array $weekDayAbbrs2 = [
        'be' => ['Пн', 'Аў', 'Ср', 'Чц', 'Пт', 'Сб', 'Нд'],
        'en' => ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
        'ru' => ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
    ];

    protected static array $weekDayAbbrs3 = [
        'be' => ['Пнд', 'Аўт', 'Сер', 'Чцв', 'Пят', 'Суб', 'Няд'],
        'en' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        'ru' => ['Пнд', 'Втр', 'Срд', 'Чтв', 'Птн', 'Сбт', 'Вск'],
    ];

    #[ArrayShape([
        'monthNames' => "array|\string[][]",
        'weekDayNames' => "array|\string[][]",
        'weekDayAbbrs2' => "array|\string[][]",
        'weekDayAbbrs3' => "array|\string[][]"
    ])]
    public static function getAll(): array
    {
        return [
            'monthNames' => static::$monthNames,
            'weekDayNames' => static::$weekDayNames,
            'weekDayAbbrs2' => static::$weekDayAbbrs2,
            'weekDayAbbrs3' => static::$weekDayAbbrs3,
        ];
    }

    public static function getMonthNames(string $language = 'en', array $keys = []): array
    {
        $monthNames = static::$monthNames[$language] ?? static::$monthNames['en'];
        if ($keys) {
            $monthNames = array_combine($keys, $monthNames);
        }
        return $monthNames;
    }

    public static function getWeekDayNames(string $language = 'en', array $keys = []): array
    {
        $weekDayNames = static::$weekDayNames[$language] ?? static::$weekDayNames['en'];
        if ($keys) {
            $weekDayNames = array_combine($keys, $weekDayNames);
        }
        return $weekDayNames;
    }

    public static function getWeekDayAbbrs2(string $language = 'en', array $keys = []): array
    {
        $weekDayAbbrs2 = static::$weekDayAbbrs2[$language] ?? static::$weekDayAbbrs2['en'];
        if ($keys) {
            $weekDayAbbrs2 = array_combine($keys, $weekDayAbbrs2);
        }
        return $weekDayAbbrs2;
    }

    public static function getWeekDayAbbrs3(string $language = 'en', array $keys = []): array
    {
        $weekDayAbbrs3 = static::$weekDayAbbrs3[$language] ?? static::$weekDayAbbrs3['en'];
        if ($keys) {
            $weekDayAbbrs3 = array_combine($keys, $weekDayAbbrs3);
        }
        return $weekDayAbbrs3;
    }

    public static function getIndexedMonthNames(string $language = 'en'): array
    {
        return array_combine(self::getMonthNames() /* [!] Without $language */, self::getMonthNames($language));
    }

    public static function getIndexedWeekDayNames(string $language = 'en'): array
    {
        return array_combine(self::getWeekDayNames() /* [!] Without $language */, self::getWeekDayNames($language));
    }

    public static function getIndexedWeekDayAbbrs2(string $language = 'en'): array
    {
        return array_combine(self::getWeekDayAbbrs2() /* [!] Without $language */, self::getWeekDayAbbrs2($language));
    }

    public static function getIndexedWeekDayAbbrs3(string $language = 'en'): array
    {
        return array_combine(self::getWeekDayAbbrs3() /* [!] Without $language */, self::getWeekDayAbbrs3($language));
    }
}
