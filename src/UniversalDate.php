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

    public function __construct($date = 'now', ?string $timezone = 'UTC')
    {
        $this->timezone = $timezone;
        $this->parseDate($date);
    }

    public static function make($date = 'now', ?string $timezone = 'UTC'): static
    {
        return new static($date, $timezone);
    }

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

    public function toHuman(?string $format = null): string
    {
        if ($format) {
            return $this->dateTime->format($format);
        }
        
        return $this->dateTime->format('F j, Y \a\t g:i A');
    }

    public function toTimeAgo(): string
    {
        $now = new DateTime('now', new DateTimeZone($this->timezone));
        $diff = $now->diff($this->dateTime);
    
        if ($this->dateTime > $now) {
            return $this->getFutureString($diff, $now);
        }
    
        return $this->getPastString($diff);
    }
    

    private function getFutureString(\DateInterval $diff, DateTime $now): string
    {
        // Handle Years (with precise day calculations)
        if ($diff->y > 0) {
            // Add the days of the remaining month
            $remainingDaysInYear = $now->diff($this->dateTime->add(new DateInterval("P" . $diff->y . "Y")))->days;
            $remainingMonths = $remainingDaysInYear / 30;
            return 'in ' . $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' and ' . ceil($remainingMonths) . ' month' . ($remainingMonths > 1 ? 's' : '');
        }
    
        // Handle Months (with precise day calculations)
        if ($diff->m > 0) {
            // Add days to months for more accurate calculation
            return 'in ' . $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' and ' . $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
        }
    
        // Handle Days
        if ($diff->d > 0) {
            return 'in ' . $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' and ' . $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
        }
    
        // Handle Hours and Minutes
        if ($diff->h > 0) {
            return 'in ' . $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' and ' . $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        }
    
        // Minutes
        if ($diff->i > 0) {
            return 'in ' . $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        }
    
        return 'soon';
    }
    
    

    private function getPastString(\DateInterval $diff): string
    {
        // Years
        if ($diff->y > 0) {
            $remainingMonths = $diff->m;
            if ($remainingMonths > 0) {
                return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' and ' . $remainingMonths . ' month' . ($remainingMonths > 1 ? 's' : '') . ' ago';
            }
            return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        }
    
        // Months
        if ($diff->m > 0) {
            $remainingDays = $diff->d;
            if ($remainingDays > 0) {
                return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' and ' . $remainingDays . ' day' . ($remainingDays > 1 ? 's' : '') . ' ago';
            }
            return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        }
    
        // Days
        if ($diff->d > 0) {
            $remainingHours = $diff->h;
            if ($remainingHours > 0) {
                return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' and ' . $remainingHours . ' hour' . ($remainingHours > 1 ? 's' : '') . ' ago';
            }
            return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        }
    
        // Hours
        if ($diff->h > 0) {
            $remainingMinutes = $diff->i;
            if ($remainingMinutes > 0) {
                return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' and ' . $remainingMinutes . ' minute' . ($remainingMinutes > 1 ? 's' : '') . ' ago';
            }
            return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        }
    
        // Minutes
        if ($diff->i > 0) {
            return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        }
    
        return 'just now';
    }
    
    public function format(string $format): string
    {
        return $this->dateTime->format($format);
    }

    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

    public function setTimezone(string $timezone): self
    {
        $this->timezone = $timezone;
        $this->dateTime->setTimezone(new DateTimeZone($timezone));
        return $this;
    }
}
