[![Packagist Downloads](https://img.shields.io/packagist/dm/astral/php-serialize)](https://packagist.org/packages/astral/php-serialize)

# Languages

- [Complete documen-English](./docs/en/README.md)
- [完整文档-中文](./docs/zh/README.md)

# php-serialize
An advanced PHP serialization tool leveraging attributes for flexible object-to-array and JSON conversion. Supports property aliases, type conversions, and nested object handling. Ideal for APIs, data persistence, and configuration management.


## Quick Start

### Installation

Install using Composer:

```bash
composer require astral/serialize
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
