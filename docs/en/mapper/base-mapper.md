## Type Conversion

### Basic Type Conversion

#### Method 1: Constructor Property Promotion

```php
use Astral\Serialize\Serialize;

class Profile extends Serialize {
    public function __construct(
        public string $username,
        public int $score,
        public float $balance,
        public bool $isActive
    ) {}
}
```

#### Method 2: Traditional Property Definition

```php
use Astral\Serialize\Serialize;

class Profile extends Serialize {
    public string $username;
    public int $score;
    public float $balance;
    public bool $isActive;
}

// Both methods support the same type conversion
$profile = Profile::from([
    'username' => 123,        // Integer to string conversion
    'score' => '100',         // String to integer conversion
    'balance' => '99.99',     // String to float conversion
    'isActive' => 1           // Number to boolean conversion
]);

// Convert to array
$profileArray = $profile->toArray();
```

#### Method 3: Readonly Properties

```php
use Astral\Serialize\Serialize;

class Profile extends Serialize {
    public readonly string $username;
    public readonly int $score;
    public readonly float $balance;
    public readonly bool $isActive;

    // Manual initialization
    public function __construct(
        string $username, 
        int $score, 
        float $balance, 
        bool $isActive
    ) {
        $this->username = $username;
        $this->score = $score;
        $this->balance = $balance;
        $this->isActive = $isActive;
    }
}
```

No matter which method is used, the `Serialize` class works properly and provides the same type conversion and serialization features.