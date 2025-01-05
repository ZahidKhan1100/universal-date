<?php

namespace MuhammadZahid\UniversalDate;

use DateTime;
use DateTimeZone;
use Exception;

/**
 * UniversalDate class for handling various date formats and conversions
 * 
 * This class provides methods to convert and format dates in various ways including
 * human readable formats, time ago representations, and specific format conversions.
 * 
 * @package MuhammadZahid\UniversalDate
 * @author Muhammad Zahid
 */
class UniversalDate
{
    /** @var DateTime */
    private $dateTime;

    /** @var string */
    private $timezone;

    /**
     * Constructor
     * 
     * @param string|int|DateTime $date Input date in any format
     * @param string|null $timezone Timezone (default: UTC)
     * @throws Exception If date parsing fails
     */
    public function __construct($date = 'now', ?string $timezone = 'UTC')
    {
        $this->timezone = $timezone;
        $this->parseDate($date);
    }

    /**
     * Static constructor method
     * 
     * @param string|int|DateTime $date Input date in any format
     * @param string|null $timezone Timezone (default: UTC)
     * @return static
     * @throws Exception If date parsing fails
     */
    public static function make($date = 'now', ?string $timezone = 'UTC'): static
    {
        return new static($date, $timezone);
    }

    /**
     * Parse various date formats into DateTime object
     * 
     * @param string|int|DateTime $date
     * @return void
     * @throws Exception
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
     * Convert date to human readable format
     * 
     * @param string $format Custom format string (optional)
     * @return string
     */
    public function toHuman(?string $format = null): string
    {
        if ($format) {
            return $this->dateTime->format($format);
        }
        
        return $this->dateTime->format('F j, Y \a\t g:i A');
    }

    /**
     * Convert to time ago format (e.g., "2 hours ago")
     * 
     * @return string
     */
    public function toTimeAgo(): string
    {
        $now = new DateTime('now', new DateTimeZone($this->timezone));
        $interval = $this->dateTime->getTimestamp() - $now->getTimestamp();
        $diff = $now->diff($this->dateTime);
        
        if ($interval > 0) {
            return $this->getFutureString($diff, $interval);
        }
        
        return $this->getPastString($diff);
    }

    /**
     * Get future time difference string
     * 
     * @param \DateInterval $diff
     * @param int $interval Seconds until the future date
     * @return string
     */
    private function getFutureString(\DateInterval $diff, int $interval): string
    {
        // If less than 2 minutes away
        if ($interval < 120) {
            return 'soon';
        }

        // Calculate exact values
        $totalMinutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
        $totalHours = $totalMinutes / 60;
        $totalDays = $diff->days;
        $totalMonths = ($diff->y * 12) + $diff->m;

        // Years
        if ($totalMonths >= 23) {
            $years = floor($totalMonths / 12);
            return 'in ' . $years . ' year' . ($years > 1 ? 's' : '');
        }

        // Months (if more than 30 days)
        if ($totalDays >= 30) {
            $months = ceil($totalDays / 30);
            return 'in ' . $months . ' month' . ($months > 1 ? 's' : '');
        }

        // Days (if more than 23 hours)
        if ($totalHours >= 23.5) {
            $days = ceil($totalHours / 24);
            return 'in ' . $days . ' day' . ($days > 1 ? 's' : '');
        }

        // Hours (if more than 90 minutes)
        if ($totalMinutes >= 90) {
            $hours = ceil($totalMinutes / 60);
            return 'in ' . $hours . ' hour' . ($hours > 1 ? 's' : '');
        }

        // Minutes
        return 'in ' . $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
    }

    /**
     * Get past time difference string
     * 
     * @param \DateInterval $diff
     * @return string
     */
    private function getPastString(\DateInterval $diff): string
    {
        if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        
        return 'just now';
    }

    /**
     * Format date to specific format
     * 
     * @param string $format PHP date format string
     * @return string
     */
    public function format(string $format): string
    {
        return $this->dateTime->format($format);
    }

    /**
     * Get DateTime instance
     * 
     * @return DateTime
     */
    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

    /**
     * Set timezone
     * 
     * @param string $timezone
     * @return self
     */
    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;
        $this->dateTime->setTimezone(new DateTimeZone($timezone));
        return $this;
    }
}
