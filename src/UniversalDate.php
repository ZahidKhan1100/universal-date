<?php

namespace MuhammadZahid\UniversalDate;

use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;

class UniversalDate
{
    /** @var DateTime */
    private $dateTime;

    /** @var string */
    private $timezone;

    /**
     * Constructor
     * 
     * @param string|DateTime $date The date to initialize with (default 'now')
     * @param string|null $timezone The timezone for the date (default 'UTC')
     */
    public function __construct($date = 'now', ?string $timezone = 'UTC')
    {
        $this->timezone = $timezone;
        $this->parseDate($date);
    }

    /**
     * Static method to create a new instance of UniversalDate
     * 
     * @param string|DateTime $date The date to initialize with (default 'now')
     * @param string|null $timezone The timezone for the date (default 'UTC')
     * @return static
     */
    public static function make($date = 'now', ?string $timezone = 'UTC'): static
    {
        return new static($date, $timezone);
    }

    /**
     * Parse the input date into a DateTime object
     * 
     * @param string|DateTime $date The date to parse
     * @throws Exception If the date format cannot be parsed
     */
    private function parseDate($date): void
    {
        if ($date instanceof DateTime) {
            $this->dateTime = $date;
        } elseif (is_numeric($date)) {
            $this->dateTime = new DateTime();
            $this->dateTime->setTimestamp($date);
        } else {
            try {
                $this->dateTime = new DateTime($date);
            } catch (Exception $e) {
                throw new Exception("Unable to parse date format: " . $date);
            }
        }

        if ($this->timezone) {
            $this->dateTime->setTimezone(new DateTimeZone($this->timezone));
        }
    }

    /**
     * Format the date in a human-readable way
     * 
     * @param string|null $format Custom format string
     * @return string Formatted date string
     */
    public function toHuman(?string $format = null): string
    {
        if ($format) {
            return $this->dateTime->format($format);
        }
        
        return $this->dateTime->format('F j, Y \a\t g:i A');
    }

    /**
     * Get a human-readable 'time ago' or 'time in future' string
     * 
     * @return string Time difference in human-readable format
     */
    public function toTimeAgo(): string
    {
        $now = new DateTime('now', new DateTimeZone($this->timezone));
        $diff = $this->dateTime->diff($now);

        if ($this->dateTime > $now) {
            return $this->getFutureString($diff, $now);
        }

        return $this->getPastString($diff);
    }

    /**
     * Generate string for future dates
     * 
     * @param DateInterval $diff Difference between dates
     * @param DateTime $now Current time
     * @return string
     */
    private function getFutureString(DateInterval $diff, DateTime $now): string
    {
        $futureDate = clone $this->dateTime;

        if ($diff->y > 0) {
            $futureDate->add(new DateInterval('P' . $diff->y . 'Y'));
            $intervalToNow = $now->diff($futureDate);
            return 'in ' . $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' and ' . $intervalToNow->m . ' month' . ($intervalToNow->m > 1 ? 's' : '');
        }

        if ($diff->m > 0) {
            $futureDate->add(new DateInterval('P' . $diff->m . 'M'));
            $intervalToNow = $now->diff($futureDate);
            return 'in ' . $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' and ' . $intervalToNow->d . ' day' . ($intervalToNow->d > 1 ? 's' : '');
        }

        if ($diff->d > 0) {
            return 'in ' . $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' and ' . $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
        }

        if ($diff->h > 0) {
            return 'in ' . $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' and ' . $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        }

        if ($diff->i > 0) {
            return 'in ' . $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        }

        return 'soon';
    }

    /**
     * Generate string for past dates
     * 
     * @param DateInterval $diff Difference between dates
     * @return string
     */
    private function getPastString(DateInterval $diff): string
    {
        if ($diff->y > 0) {
            return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        }

        if ($diff->m > 0) {
            return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        }

        if ($diff->d > 0) {
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        }

        if ($diff->h > 0) {
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        }

        if ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        }

        return 'just now';
    }

    /**
     * Format the date using a specific format
     * 
     * @param string $format PHP date format string
     * @return string Formatted date string
     */
    public function format(string $format): string
    {
        return $this->dateTime->format($format);
    }

    /**
     * Get the internal DateTime object
     * 
     * @return DateTime
     */
    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

    /**
     * Set a new timezone for the date
     * 
     * @param string $timezone New timezone string
     * @return self Returns the instance for method chaining
     */
    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;
        $this->dateTime->setTimezone(new DateTimeZone($timezone));
        return $this;
    }
}

