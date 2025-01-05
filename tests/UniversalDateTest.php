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

    /**
     * @test
     * @dataProvider timeDifferenceProvider
     */
    public function it_handles_time_ago_for_future_dates($input, $expected)
    {
        $result = UniversalDate::make($input)->toTimeAgo();
        $this->assertEquals($expected, $result, "Failed asserting that '{$result}' matches expected '{$expected}' for input '{$input}'");
    }

    public function timeDifferenceProvider()
    {
        return [
            // Very near future
            'seconds_10' => ['+10 seconds', 'soon'],
            'seconds_45' => ['+45 seconds', 'soon'],
            
            // Minutes
            'minute_1' => ['+1 minute', 'in 1 minute'],
            'minute_30' => ['+30 minutes', 'in 30 minutes'],
            'minute_44' => ['+44 minutes', 'in 44 minutes'],
            
            // Hour boundaries
            'hour_0.75' => ['+45 minutes', 'in 1 hour'],
            'hour_1' => ['+1 hour', 'in 1 hour'],
            'hour_1.25' => ['+1 hour 15 minutes', 'in 1 hour'],
            'hour_1.75' => ['+1 hour 45 minutes', 'in 2 hours'],
            'hour_2' => ['+2 hours', 'in 2 hours'],
            'hour_23' => ['+23 hours', 'in 23 hours'],
            'hour_23.5' => ['+23 hours 30 minutes', 'in 1 day'],
            
            // Day boundaries
            'day_1_exact' => ['+24 hours', 'in 1 day'],
            'day_1_over' => ['+25 hours', 'in 1 day'],
            'day_1_native' => ['+1 day', 'in 1 day'],
            'day_2' => ['+2 days', 'in 2 days'],
            'day_6' => ['+6 days', 'in 6 days'],
            'day_29' => ['+29 days', 'in 29 days'],
            
            // Month boundaries
            'month_1_exact' => ['+30 days', 'in 1 month'],
            'month_1_over' => ['+32 days', 'in 1 month'],
            'month_1_native' => ['+1 month', 'in 1 month'],
            'month_2' => ['+2 months', 'in 2 months'],
            'month_11' => ['+11 months', 'in 11 months'],
            'month_11.5' => ['+11 months 15 days', 'in 1 year'],
            
            // Year boundaries
            'year_1_exact' => ['+12 months', 'in 1 year'],
            'year_1_native' => ['+1 year', 'in 1 year'],
            'year_1_over' => ['+13 months', 'in 1 year'],
            'year_2' => ['+2 years', 'in 2 years'],
            'year_2.5' => ['+2 years 6 months', 'in 2 years'],
            'year_5' => ['+5 years', 'in 5 years'],
            
            // Additional edge cases
            'year_0.9' => ['+11 months', 'in 11 months'],
            'year_0.95' => ['+11 months 15 days', 'in 1 year'],
            'year_1.1' => ['+13 months', 'in 1 year'],
            'day_0.9' => ['+22 hours', 'in 22 hours'],
            'day_0.95' => ['+23 hours', 'in 1 day']
        ];
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
