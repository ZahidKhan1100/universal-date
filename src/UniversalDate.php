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
            return $this->getFutureString($diff, $now, $this->dateTime);
        }
        
        return $this->getPastString($diff);
    }

    private function getFutureString(\DateInterval $diff, DateTime $now, DateTime $future): string
    {
        $totalSeconds = $future->getTimestamp() - $now->getTimestamp();

        // Less than 60 seconds
        if ($totalSeconds < 60) {
            return 'soon';
        }

        // Get exact values
        $minutes = floor($totalSeconds / 60);
        $hours = floor($totalSeconds / 3600);
        $days = floor($totalSeconds / 86400);
        $months = ($diff->y * 12) + $diff->m;
        
        // Check partial months for rounding
        if ($diff->d >= 15) {
            $months++;
        }

        // Years calculation (12 months or more)
        if ($months >= 12 || $diff->y > 0) {
            $years = max($diff->y, floor($months / 12));
            return 'in ' . $years . ' year' . ($years > 1 ? 's' : '');
        }

        // Months (30 days or more)
        if ($months > 0 || $days >= 30) {
            $monthCount = $months > 0 ? $months : 1;
            return 'in ' . $monthCount . ' month' . ($monthCount > 1 ? 's' : '');
        }

        // Days (23 hours or more)
        if ($hours >= 23) {
            $dayCount = $hours >= 23.5 ? ceil($hours / 24) : floor($hours / 24);
            return 'in ' . $dayCount . ' day' . ($dayCount > 1 ? 's' : '');
        }

        // Hours (45 minutes or more)
        if ($minutes >= 45) {
            $hourCount = ceil($minutes / 60);
            return 'in ' . $hourCount . ' hour' . ($hourCount > 1 ? 's' : '');
        }

        // Minutes
        if ($minutes > 0) {
            $exactMinutes = $totalSeconds / 60;
            $roundedMinutes = $exactMinutes >= 1 ? round($exactMinutes) : ceil($exactMinutes);
            return 'in ' . $roundedMinutes . ' minute' . ($roundedMinutes > 1 ? 's' : '');
        }

        return 'in 1 minute';
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
