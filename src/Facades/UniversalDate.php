<?php

namespace MuhammadZahid\UniversalDate\Facades;

use DateTime;
use Illuminate\Support\Facades\Facade;
use MuhammadZahid\UniversalDate\UniversalDate as UniversalDateClass;

/**
 * @method static string toHuman(?string $format = null)
 * @method static string toTimeAgo()
 * @method static string format(string $format)
 * @method static DateTime getDateTime()
 * @method static UniversalDateClass setTimezone(string $timezone)
 * @method static UniversalDateClass make(string|int|DateTime $date = 'now', ?string $timezone = 'UTC')
 *
 * @see \MuhammadZahid\UniversalDate\UniversalDate
 */
class UniversalDate extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'universaldate';
    }
}
