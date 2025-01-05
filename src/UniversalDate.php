<?php

namespace MuhammadZahid\UniversalDate;

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
        $interval = $this->dateTime->getTimestamp() - $now->getTimestamp();
        $diff = $now->diff($this->dateTime);
        
        if ($interval > 0) {
            return $this->getFutureString($diff);
        }
        
        return $this->getPastString($diff);
    }

    private function getFutureString(\DateInterval $diff): string
    {
        // Calculate all intervals exactly
        $totalMinutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
        
        // Less than 1 minute = soon
        if ($totalMinutes < 1) {
            return 'soon';
        }

        // Years calculation (use exact months)
        $totalMonths = ($diff->y * 12) + $diff->m;
        if ($totalMonths >= 12) {
            $years = floor($totalMonths / 12);
            return 'in ' . $years . ' year' . ($years > 1 ? 's' : '');
        }

        // Months calculation (30 days = 1 month)
        if ($diff->days >= 30 || $totalMonths > 0) {
            $months = $totalMonths > 0 ? $totalMonths : floor($diff->days / 30);
            return 'in ' . $months . ' month' . ($months > 1 ? 's' : '');
        }

        // Days calculation (23 hours = 1 day)
        $totalHours = ($diff->days * 24) + $diff->h;
        if ($totalHours >= 23) {
            $days = ceil($totalHours / 24);
            return 'in ' . $days . ' day' . ($days > 1 ? 's' : '');
        }

        // Hours calculation (45 minutes = 1 hour)
        if ($totalMinutes >= 45) {
            $hours = ceil($totalMinutes / 60);
            return 'in ' . $hours . ' hour' . ($hours > 1 ? 's' : '');
        }

        // Minutes (exact minutes)
        return 'in ' . ceil($totalMinutes) . ' minute' . (ceil($totalMinutes) > 1 ? 's' : '');
    }

    private function getPastString(\DateInterval $diff): string
    {
        if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        
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
