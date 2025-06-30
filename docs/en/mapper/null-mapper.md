## Null值转换规则详细示例

当属性不是可空类型（`?type`）时，`null` 值会根据目标类型自动转换：

```php
use Astral\Serialize\Serialize;

class NullConversionProfile extends Serialize {
    public string $username;
    public int $score;
    public float $balance;
    public array $tags;
    public object $metadata;
}

// Null 值转换示例
$profile = NullConversionProfile::from([
    'username' => null,   // 转换为空字符串 ''
    'score' => null,      // 转换为 0
    'balance' => null,    // 转换为 0.0
    'tags' => null,       // 转换为空数组 []
    'metadata' => null    // 转换为空对象 new stdClass()
]);

// 验证转换结果
echo $profile->username;   // 输出: ""（空字符串）
echo $profile->score;      // 输出: 0
echo $profile->balance;    // 输出: 0.0
var_dump($profile->tags);  // 输出: array(0) {}
var_dump($profile->metadata);  // 输出: object(stdClass)#123 (0) {}

// 布尔值的特殊处理
try {
    NullConversionProfile::from([
        'isActive' => null  // 这将抛出类型错误
    ]);
} catch (\TypeError $e) {
    echo "布尔类型不支持 null 值：" . $e->getMessage();
}
```

## 可空类型的方案

对于需要接受 `null` 的场景，使用可空类型：

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

// 创建包含 null 值的对象
$profile = FlexibleProfile::from([
    'username' => null,           // 允许 null
    'score' => null,              // 允许 null
    'metadata' => null,           // 允许 null
    'tags' => null                // 允许 null
]);

// 转换为数组
$profileArray = $profile->toArray();
// $profileArray 的内容:
// [
//     'username' => null,
//     'score' => null,
//     'metadata' => null,
//     'tags' => null
// ]

// 验证可空类型的行为
echo $profile->username;         // 输出 null
```