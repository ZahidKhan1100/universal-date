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
        // Less than 60 seconds
        if ($interval < 60) {
            return 'soon';
        }

        // Get exact values
        $totalMinutes = floor($interval / 60);
        $totalHours = floor($interval / 3600);
        $totalDays = floor($interval / 86400);

        // Calculate months more precisely
        $months = ($diff->y * 12) + $diff->m;
        if ($diff->d >= 15) {
            $months++;
        }

        // Years (12+ months)
        if ($months >= 11 && $diff->d >= 15) {
            $years = max(1, $diff->y);
            return 'in ' . $years . ' year' . ($years > 1 ? 's' : '');
        }

        // Months (30+ days)
        if ($months > 0 || $totalDays >= 30) {
            return 'in ' . $months . ' month' . ($months > 1 ? 's' : '');
        }

        // Days (23+ hours)
        if ($totalHours >= 23) {
            if ($totalHours == 23 && $diff->i < 30) {
                return 'in 23 hours';
            }
            return 'in ' . ($totalDays + 1) . ' day' . ($totalDays + 1 > 1 ? 's' : '');
        }

        // Hours (45+ minutes)
        if ($totalMinutes >= 45) {
            if ($diff->i >= 45) {
                return 'in ' . ($totalHours + 1) . ' hour' . ($totalHours + 1 > 1 ? 's' : '');
            }
            return 'in ' . $totalHours . ' hour' . ($totalHours > 1 ? 's' : '');
        }

        // Minutes
        return 'in ' . $totalMinutes . ' minute' . ($totalMinutes > 1 ? 's' : '');
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
