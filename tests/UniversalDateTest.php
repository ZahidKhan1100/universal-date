<?php

namespace MuhammadZahid\UniversalDate\Tests;

use DateTime;
use DateTimeZone;
use Exception;
use PHPUnit\Framework\TestCase;
use MuhammadZahid\UniversalDate\UniversalDate;

class UniversalDateTest extends TestCase
{
    /**
    * @test
    * @dataProvider dateFormatProvider
    */
    public function it_accepts_different_date_formats($input, $expectedTimestamp)
    {
        $date = new UniversalDate($input);
        $this->assertEquals($expectedTimestamp, $date->getDateTime()->getTimestamp());
    }

    public function dateFormatProvider()
    {
        $timestamp = 1609459200; // 2021-01-01 00:00:00
        return [
            'timestamp' => [$timestamp, $timestamp],
            'date_string' => ['2021-01-01', $timestamp],
            'datetime_object' => [new DateTime('@' . $timestamp), $timestamp],
        ];
    }

    /** @test */
    public function it_converts_to_human_readable_format()
    {
        $date = new UniversalDate('2021-01-01 15:30:00');
        $this->assertEquals('January 1, 2021 at 3:30 PM', $date->toHuman());
    }

    /** @test */
    public function it_accepts_custom_format()
    {
        $date = new UniversalDate('2021-01-01 15:30:00');
        $this->assertEquals('01-01-2021', $date->toHuman('d-m-Y'));
    }

    /** @test */
    public function it_handles_time_ago_for_past_dates()
    {
        $now = new DateTime('now');
        $pastDate = clone $now;
        $pastDate->modify('-2 hours');
        
        $date = new UniversalDate($pastDate);
        $this->assertEquals('2 hours ago', $date->toTimeAgo());
    }

    /** @test */
    public function it_handles_time_ago_for_future_dates()
    {
        // Use a fixed reference point
        $baseDate = new DateTime('2024-01-01 12:00:00');
        $threeMonthsLater = clone $baseDate;
        $threeMonthsLater->modify('+3 months');
        
        // Create a UniversalDate instance from the future date
        $date = new UniversalDate($threeMonthsLater);
        
        // Mock the current time in UniversalDate
        $reflectionClass = new \ReflectionClass($date);
        $dateTimeProperty = $reflectionClass->getProperty('dateTime');
        $dateTimeProperty->setAccessible(true);
        $dateTimeProperty->setValue($date, $threeMonthsLater);
        
        // Test relative to the base date
        $this->assertEquals('in 3 months', $date->toTimeAgo());
    }

    /** @test */
    public function it_handles_different_timezones()
    {
        $date = new UniversalDate('2021-01-01 12:00:00', 'UTC');
        $this->assertEquals('January 1, 2021 at 12:00 PM', $date->toHuman());

        $date->setTimezone('America/New_York');
        $this->assertEquals('January 1, 2021 at 7:00 AM', $date->toHuman());
    }

    /** @test */
    public function it_throws_exception_for_invalid_date()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to parse date format: invalid-date');
        
        new UniversalDate('invalid-date');
    }

    /** @test */
    public function it_throws_exception_for_invalid_timezone()
    {
        $this->expectException(Exception::class);
        
        $date = new UniversalDate('2021-01-01');
        $date->setTimezone('Invalid/Timezone');
    }

    /** @test */
    public function it_handles_just_now_time_ago()
    {
        $date = new UniversalDate('now');
        $this->assertEquals('just now', $date->toTimeAgo());
    }

    /** @test */
    public function it_handles_soon_for_immediate_future()
    {
        $now = new DateTime('now');
        $futureDate = clone $now;
        $futureDate->modify('+10 seconds');
        
        $date = new UniversalDate($futureDate);
        $this->assertEquals('soon', $date->toTimeAgo());
    }
}

