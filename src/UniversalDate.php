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
        
        return $this->dateTime->format('Y-m-d H:i:s'); // Changed to the specified format
    }


    /**
     * Get a human-readable 'time ago' or 'time in future' string
     * 
     * @return string Time difference in human-readable format
     */
    public function toTimeAgo(): string
    {
        $now = new DateTime('now', new DateTimeZone($this->timezone));
        $futureDate = clone $this->dateTime;
    
        // No need to add 'P1M' here since it's already added when making the object
        return $futureDate->format('Y-m-d H:i:s');
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
    
     
        // Years
        if ($diff->y > 0) {
            $futureDate->add(new DateInterval('P' . $diff->y . 'Y'));
            $intervalToNow = $now->diff($futureDate);
            return 'in ' . $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
        }
    
        // Months
        if ($diff->m > 0) {
            $futureDate->add(new DateInterval('P' . $diff->m . 'M'));
            $intervalToNow = $now->diff($futureDate);
            return 'in ' . $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' and ' . $intervalToNow->d . ' day' . ($intervalToNow->d > 1 ? 's' : '');
        }
    
        // Days
        if ($diff->d > 0) {
            $futureDate->add(new DateInterval('P' . $diff->d . 'D'));
            $intervalToNow = $now->diff($futureDate);
            return 'in ' . $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' and ' . $intervalToNow->h . ' hour' . ($intervalToNow->h > 1 ? 's' : '');
        }
    
        // Hours
        if ($diff->h > 0) {
            $futureDate->add(new DateInterval('PT' . $diff->h . 'H'));
            $intervalToNow = $now->diff($futureDate);
            // Check if adding hours resulted in crossing into the next day
            if ($intervalToNow->d > 0) {
                return 'in ' . $intervalToNow->d . ' day' . ($intervalToNow->d > 1 ? 's' : '') . ' and ' . $intervalToNow->h . ' hour' . ($intervalToNow->h > 1 ? 's' : '');
            }
            return 'in ' . $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' and ' . $intervalToNow->i . ' minute' . ($intervalToNow->i > 1 ? 's' : '');
        }
    
        // Minutes
        if ($diff->i > 0) {
            $futureDate->add(new DateInterval('PT' . $diff->i . 'M'));
            $intervalToNow = $now->diff($futureDate);
            // Check if adding minutes resulted in crossing into the next hour
            if ($intervalToNow->h > 0) {
                return 'in ' . $intervalToNow->h . ' hour' . ($intervalToNow->h > 1 ? 's' : '') . ' and ' . $intervalToNow->i . ' minute' . ($intervalToNow->i > 1 ? 's' : '');
            }
            return 'in ' . $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        }
    
        // Seconds
        if ($diff->s > 0) {
            return 'in ' . $diff->s . ' second' . ($diff->s > 1 ? 's' : '');
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
        $pastDate = clone $this->dateTime;
        $pastDate->sub($diff);
        return $pastDate->format('Y-m-d H:i:s');
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

