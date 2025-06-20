## 快速开始

### 安装

使用 Composer 安装：

```bash
composer require astral/serialize
```

### 基本用法

```php
use Astral\Serialize\Serialize;

class User extends Serialize {
    public string $name,
    public int $age
}

// 从数组创建对象
$user = User::from([
    'name' => '张三',
    'age' => 30
]);

// 访问对象属性
echo $user->name;  // 输出: 张三
echo $user->age;   // 输出: 30

// 转换为数组
$userArray = $user->toArray();
// $userArray 的内容:
// [
//     'name' => '张三',
//     'age' => 30
// ]
```

### 其他特性

#### **不可变性**：只读属性在构造后无法修改

```php
use Astral\Serialize\Serialize;

class User extends Serialize {
    public function __construct(
        public readonly string $name,
        public readonly int $age
    ) {}
}

$user = User::from([
    'name' => '张三',
    'age' => 30
]);

try {
    $user->name = '李四';  // 编译时错误：无法修改只读属性
} catch (Error $e) {
    echo "只读属性不能被重新赋值";
}
```

#### **类型安全的初始化**

```php
$user = User::from([
    'name' => 123,       // 整数会被转换为字符串
    'age' => '35'        // 字符串会被转换为整数
]);

echo $user->name;  // 输出: "123"
echo $user->age;   // 输出: 35
```

#### **构造函数初始化**

```php
use Astral\Serialize\Serialize;

class User extends Serialize {
    public function __construct(
        public readonly string $name,
        public readonly int $age
    ) {
        // 可以在构造函数中添加额外的验证或处理逻辑
        if (strlen($name) < 2) {
            throw new \InvalidArgumentException('名称太短');
        }
    }
}
```