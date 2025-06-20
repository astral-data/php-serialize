[![Total Downloads](https://img.shields.io/packagist/dt/astral/php-serialize.svg?style=flat-square)](https://packagist.org/packages/astral/php-serialize)
[![Tests](https://github.com/astral-data/php-serialize/actions/workflows/test.yml/badge.svg)](https://github.com/astral-data/php-serialize/actions/workflows/test.yml)
[![PHPStan](https://github.com/astral-data/php-serialize/actions/workflows/phpstan.yml/badge.svg)](https://github.com/astral-data/php-serialize/actions/workflows/phpstan.yml)

# Languages

- [Complete documen-English](https://astrals-organization.gitbook.io/php-serialize/php-serialize-en)
- [完整文档-中文](https://astrals-organization.gitbook.io/php-serialize)

# php-serialize

**php-serialize** is a powerful attribute-based serialization library for PHP (requires **PHP ≥ 8.1**).  
It allows you to map objects to arrays/JSON and **automatically generate OpenAPI documentation** based on the same attributes.

> 🚀 Unified solution for API data serialization and documentation generation.

## ✨ Features

- 🏷️ Property aliasing with
- 🔄 Automatic type casting (e.g. `DateTime ↔ string`)
- 🔁 Deep object nesting support
- ❌ Skip/exclude fields with
- 🧩 Recursive DTO serialization
- 🧬 **Auto-generate OpenAPI schema** using object definitions
- ⚙️ Framework-agnostic — works with Laravel, Symfony, etc.

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
