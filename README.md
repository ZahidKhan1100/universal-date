# UniversalDate

A powerful PHP package for converting dates into human-readable formats with Laravel integration support. Convert any type of date format into user-friendly, readable text with support for multiple languages and "time ago" formatting.

## Features

- 🔄 Convert any date format to human-readable text
- ⏰ "Time ago" formatting (e.g., "2 hours ago", "in 3 days")
- 🌍 Timezone support
- 🔌 Seamless Laravel integration
- 🎯 Support for multiple date input formats (string, timestamp, DateTime)
- 🛠️ Customizable output formats
- 🚀 Simple and intuitive API

## Requirements

- PHP 7.4 or higher
- Laravel 8.0+ (for Laravel integration)

## Installation

Install the package via Composer:

```bash
composer require muhammad-zahid/universaldate
```

## Basic Usage

```php
use MuhammadZahid\UniversalDate\UniversalDate;

// Initialize with current date
$date = new UniversalDate();
echo $date->toHuman(); // "January 5, 2024 at 9:30 PM"

// Initialize with a specific date
$date = new UniversalDate('2023-12-25');
echo $date->toHuman(); // "December 25, 2023 at 12:00 AM"

// Time ago format
$date = new UniversalDate('2023-12-25');
echo $date->toTimeAgo(); // "11 days ago"

// Custom format
$date = new UniversalDate('2023-12-25');
echo $date->format('Y-m-d'); // "2023-12-25"

// With timezone
$date = new UniversalDate('2023-12-25', 'America/New_York');
echo $date->toHuman(); // Shows date in New York timezone
```

## Laravel Integration

The package includes Laravel integration with a Facade for convenient usage.

### Using the Facade

```php
use MuhammadZahid\UniversalDate\Facades\UniversalDate;

// Quick usage
echo UniversalDate::toHuman(); // Current date in human format

// Chain methods
echo UniversalDate::make('2023-12-25')
    ->setTimezone('America/New_York')
    ->toHuman();

// Time ago
echo UniversalDate::make('2023-12-25')->toTimeAgo();
```

### Using Dependency Injection

```php
use MuhammadZahid\UniversalDate\UniversalDate;

class DateController
{
    public function show(UniversalDate $date)
    {
        return $date->toHuman();
    }
}
```

## Available Methods

### Main Methods

- `toHuman(?string $format = null): string`
Converts date to human-readable format

- `toTimeAgo(): string`
Shows relative time (e.g., "2 hours ago")

- `format(string $format): string`
Formats date according to specified format string

- `getDateTime(): DateTime`
Returns the underlying DateTime instance

- `setTimezone(string $timezone): self`
Sets the timezone for the date

### Static Creation (Laravel Facade)

- `make(string|int|DateTime $date = 'now', ?string $timezone = 'UTC'): UniversalDate`
Creates a new UniversalDate instance

## License

The UniversalDate package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

## Support

If you have any questions or run into any issues, please create an issue on the GitHub repository.

