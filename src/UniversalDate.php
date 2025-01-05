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
            return $this->getFutureString($diff, $interval);
        }
        
        return $this->getPastString($diff);
    }

    private function getFutureString(\DateInterval $diff, int $interval): string
    {
        // Handle intervals less than 60 seconds
        if ($interval < 60) {
            return 'soon';
        }

        // Years
        if ($diff->y > 0) {
            return 'in ' . $diff->y . ' year' . ($diff->y > 1 ? 's' : '');
        }

        // Calculate total months including partial months
        $totalMonths = ($diff->y * 12) + $diff->m;
        if ($diff->d >= 15) { // If more than half a month
            $totalMonths++;
        }

        // 12 or more months should be shown as 1 year
        if ($totalMonths >= 12) {
            return 'in 1 year';
        }

        // Months (if we have months or 30+ days)
        if ($diff->m > 0 || $diff->d >= 30) {
            $months = $diff->m;
            if ($diff->d >= 15) { // Round up month if we're past the middle
                $months++;
            }
            return 'in ' . $months . ' month' . ($months > 1 ? 's' : '');
        }

        // Calculate total hours
        $totalHours = ($diff->d * 24) + $diff->h;
        $extraMinutes = $diff->i;

        // Days (23+ hours should show as next day)
        if ($totalHours >= 23 && $extraMinutes >= 30) {
            $days = $diff->d + 1;
            return 'in ' . $days . ' day' . ($days > 1 ? 's' : '');
        }

        if ($diff->d > 0) {
            return 'in ' . $diff->d . ' day' . ($diff->d > 1 ? 's' : '');
        }

        // Hours (45+ minutes should show as next hour)
        if ($diff->h > 0 || $diff->i >= 45) {
            $hours = $diff->h;
            if ($diff->i >= 45) {
                $hours++;
            }
            return 'in ' . $hours . ' hour' . ($hours > 1 ? 's' : '');
        }

        // Minutes
        if ($diff->i > 0) {
            return 'in ' . $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
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
