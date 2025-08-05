## Detailed Example of Null Value Conversion Rules

When a property is not a nullable type (`?type`), a `null` value will be automatically converted according to the target type:

```php
use Astral\Serialize\Serialize;

class NullConversionProfile extends Serialize {
    public string $username;
    public int $score;
    public float $balance;
    public array $tags;
    public object $metadata;
}

// Null value conversion example
$profile = NullConversionProfile::from([
    'username' => null,   // Converts to empty string ''
    'score' => null,      // Converts to 0
    'balance' => null,    // Converts to 0.0
    'tags' => null,       // Converts to empty array []
    'metadata' => null    // Converts to empty object new stdClass()
]);

// Verify conversion results
echo $profile->username;   // Output: "" (empty string)
echo $profile->score;      // Output: 0
echo $profile->balance;    // Output: 0.0
var_dump($profile->tags);  // Output: array(0) {}
var_dump($profile->metadata);  // Output: object(stdClass)#123 (0) {}

// Special handling for boolean values
try {
    NullConversionProfile::from([
        'isActive' => null  // This will throw a type error
    ]);
} catch (\TypeError $e) {
    echo "Boolean type does not support null values: " . $e->getMessage();
}
```

## Solution for Nullable Types

For scenarios that require accepting `null`, use nullable types:

```php
use Astral\Serialize\Serialize;

class FlexibleProfile extends Serialize {
    public function __construct(
        public ?string $username,
        public ?int $score,
        public ?object $metadata,
        public ?array $tags
    ) {}
}

// Create an object containing null values
$profile = FlexibleProfile::from([
    'username' => null,           // Allow null
    'score' => null,              // Allow null
    'metadata' => null,           // Allow null
    'tags' => null                // Allow null
]);

// Convert to array
$profileArray = $profile->toArray();
// Content of $profileArray:
// [
//     'username' => null,
//     'score' => null,
//     'metadata' => null,
//     'tags' => null
// ]

// Verify the behavior of nullable types
echo $profile->username;         // Output null
```