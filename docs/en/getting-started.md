## Quick Start

### Installation

Install using Composer:

```bash
composer require astral/php-serialize
```

### Basic Usage

```php
use Astral\Serialize\Serialize;

class User extends Serialize {
    public string $name,
    public int $age
}

// Create object from array
$user = User::from([
    'name' => 'John Doe',
    'age' => 30
]);

// Access object properties
echo $user->name;  // Output: John Doe
echo $user->age;   // Output: 30

// Convert to array
$userArray = $user->toArray();
// $userArray contents:
// [
//     'name' => 'John Doe',
//     'age' => 30
// ]
```

#### Other Features

1. **Immutability**: Read-only properties cannot be modified after construction

```php
use Astral\Serialize\Serialize;

class User extends Serialize {
    public function __construct(
        public readonly string $name,
        public readonly int $age
    ) {}
}

$user = User::from([
    'name' => 'John Doe',
    'age' => 30
]);

try {
    $user->name = 'Jane Doe';  // Compile-time error: cannot modify read-only property
} catch (Error $e) {
    echo "Read-only properties cannot be reassigned";
}
```

2. **Type-Safe Initialization**

```php
$user = User::from([
    'name' => 123,       // Integer will be converted to string
    'age' => '35'        // String will be converted to integer
]);

echo $user->name;  // Output: "123"
echo $user->age;   // Output: 35
```

3. **Constructor Initialization**

```php
use Astral\Serialize\Serialize;

class User extends Serialize {
    public function __construct(
        public readonly string $name,
        public readonly int $age
    ) {
        // Can add additional validation or processing logic in the constructor
        if (strlen($name) < 2) {
            throw new \InvalidArgumentException('Name is too short');
        }
    }
}
```