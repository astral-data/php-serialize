## Date/Time Conversion

1. Flexible Formatting
   Supports multiple input and output date/time formats
   Easily handles date representations from different regions
2. Timezone Handling
   Supports conversion between different timezones
   Automatically handles timezone offsets for times
3. Type Safety
   Automatically converts strings to DateTime objects
   Ensures type consistency and correctness

### Basic Usage

```php
use Astral\Serialize\Attributes\Input\InputDateFormat;
use Astral\Serialize\Attributes\Output\OutputDateFormat;
use Astral\Serialize\Serialize;

class TimeExample extends Serialize {

    // 输入时间格式转换
    #[InputDateFormat('Y-m-d')]
    public DateTime $dateTime;

    // 输入时间格式转换
    #[InputDateFormat('Y-m-d')]
    public string $dateDateString;

    // Output date format conversion
    #[OutputDateFormat('Y/m/d H:i')]
    public DateTime|string $processedAt;
    

    // Supports multiple date/time formats
    #[InputDateFormat('Y-m-d H:i:s')]
    #[OutputDateFormat('Y-m-d')]
    public string $createdAt;
}

// Create order object
$order = TimeExample::from([
    'dateTime' => new DateTime('2023-08-11'),           // Input format: Y-m-d
    'dateDateString' => '2023-08-15',           // Input format: Y-m-d
    'processedAt' => '2023-08-16 14:30',   // Default input format, also supports DateTime objects
    'createdAt' => '2023-08-16 14:45:30'   // Input format: Y-m-d H:i:s
]);

// Convert to array
$orderArray = $order->toArray();
// Content of $orderArray:
// [
//     'dateTime' => '2023-08-11',
//     'dateDateString' => '2023-08-15',
//     'processedAt' => '2023/08/16 14:30',
//     'createdAt' => '2023-08-16'
// ]
```

### Time Conversion with Timezone

```php

use Astral\Serialize\Attributes\Input\InputDateFormat;
use Astral\Serialize\Attributes\Output\OutputDateFormat;
use Astral\Serialize\Serialize;

class AdvancedTimeUser extends Serialize {
    // Supports timezone conversion
    #[InputDateFormat('Y-m-d H:i:s', timezone: 'UTC')]
    #[OutputDateFormat('Y-m-d H:i:s', timezone: 'Asia/Shanghai')]
    public DateTime $registeredAt;

    // Supports date formats from different regions
    #[InputDateFormat('d/m/Y')]  // UK format
    #[OutputDateFormat('Y-m-d')]  // Standard format
    public DateTime $birthDate;
}

// Use advanced time conversion
$advancedUser = AdvancedTimeUser::from([
    'registeredAt' => '2023-08-16 10:00:00',  // UTC time
    'birthDate' => '15/08/1990'  // UK date format
]);

$advancedUserArray = $advancedUser->toArray();
// Content of $advancedUserArray:
// [
//     'registeredAt' => '2023-08-16 18:00:00',  // Converted to Shanghai timezone
//     'birthDate' => '1990-08-15'
// ]
```