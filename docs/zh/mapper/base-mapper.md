## 类型转换

### 基本类型转换

#### 方式一：构造函数属性提升

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

#### 方式二：传统属性定义

```php
use Astral\Serialize\Serialize;

class Profile extends Serialize {
    public string $username;
    public int $score;
    public float $balance;
    public bool $isActive;
}

// 两种方式都支持相同的类型转换
$profile = Profile::from([
    'username' => 123,        // 整数转换为字符串
    'score' => '100',         // 字符串转换为整数
    'balance' => '99.99',     // 字符串转换为浮点数
    'isActive' => 1           // 数字转换为布尔值
]);

// 转换为数组
$profileArray = $profile->toArray();
```

#### 方式三：只读属性

```php
use Astral\Serialize\Serialize;

class Profile extends Serialize {
    public readonly string $username;
    public readonly int $score;
    public readonly float $balance;
    public readonly bool $isActive;

    // 手动初始化
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

无论使用哪种方式，`Serialize` 类都能正常工作，并提供相同的类型转换和序列化功能。