# Astral Serialize 文档

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

#### 其他特性

1. **不可变性**：只读属性在构造后无法修改

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

2. **类型安全的初始化**

```php
$user = User::from([
    'name' => 123,       // 整数会被转换为字符串
    'age' => '35'        // 字符串会被转换为整数
]);

echo $user->name;  // 输出: "123"
echo $user->age;   // 输出: 35
```

3. **构造函数初始化**

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

## DTO 转换

### 类型转换

#### 基本类型转换

##### 方式一：构造函数属性提升

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

##### 方式二：传统属性定义

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

##### 方式三：只读属性

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

#### 枚举转换

枚举转换提供了强大且灵活的枚举处理机制，支持多种枚举类型和转换场景。

- 支持 `tryFrom()` 和 `cases()` 方法的枚举类型
- 输入时自动将字符串转换为枚举实例
- 输出时自动将枚举转换为字符串（枚举名称）
- 提供灵活且安全的枚举处理机制

##### 普通枚举

```php
enum UserRole {
    case ADMIN;
    case EDITOR;
    case VIEWER;
}

class ComplexUser extends Serialize {

    public UserRole $role;

    // 支持多种枚举类型
    public UserStatus|UserRole $mixedStatus;
}

$complexUser = ComplexUser::from([
    'role' => 'ADMIN',           // 自动转换为 UserRole::ADMIN
    'mixedStatus' => 'ACTIVE'    // 可以是 UserStatus 或 UserRole
]);

echo $complexUser->role; // 返回 UserRole枚举实例

$complexUserArray = $complexUser->toArray();
// $complexUserArray 的内容:
// [
//     'role' => 'ADMIN',
//     'mixedStatus' => 'ACTIVE'
// ]
```

##### 回退枚举

```php
use Astral\Serialize\Serialize;

// BackedEnum
enum UserStatus: string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
}

// 定义带有枚举的用户类
class User extends Serialize {
    public string $name;

    // 支持 UnitEnum 和 BackedEnum
    public UserStatus $status;

    // 支持多枚举类型
    public UserStatus|string $alternateStatus;
}

// 创建用户对象
$user = User::from([
    'name' => '张三',
    'status' => 'active',           // 自动转换为 UserStatus::ACTIVE
    'alternateStatus' => 'inactive' // 支持字符串或枚举值
]);

// 转换为数组
$userArray = $user->toArray();
// $userArray 的内容:
// [
//     'name' => '张三',
//     'status' => 'ACTIVE',        // 输出枚举名称
//     'alternateStatus' => 'INACTIVE'
// ]
```

#### Null 值转换规则详细示例

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

#### 可空类型的方案

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

### 注解类使用

#### 属性分组

属性分组提供了一种灵活的方式来控制属性的输入和输出行为，允许在不同场景下精细地管理数据转换。

##### 基本用法

在属性上使用 `#[Groups]` 注解来指定属性所属的分组。

```php
use Astral\Serialize\Attributes\Groups;
use Astral\Serialize\Serialize;

class User extends Serialize {

    #[Groups('update','detail')]
    public string $id;

    #[Groups('create', 'update', 'detail')]
    public string $name;

    #[Groups('create','detail')]
    public string $username;

    #[Groups('other')]
    public string $sensitiveData;

    // 没有指定Group 的属性将会被默认分组在default分组中
    public string $noGroupInfo;

    // 构造函数参数也支持分组
    public function __construct(
        #[Groups('create','detail')]
        public readonly string $email,
        
        #[Groups('update','detail')]
        public readonly int $score
    ) {}
}



// 使用 默认分组展示所有信息
$user1 = User::from(
    id:1,
    name: '李四',
    score: 100,
    username: 'username',
    email: 'zhangsan@example.com',
    sensitiveData:'机密信息',
    noGroupInfo:'默认分组信息'
);

// 使用默认分组 toArray，展示所有信息
$defaultArray = $user1->toArray();
// $defaultArray 的内容:
// [
//     'id' => '1',
//     'name' => '李四',
//     'username' => 'username',
//     'score' => 100,
//     'email' => 'zhangsan@example.com',
//     'sensitiveData' => '机密信息',
//     'noGroupInfo' => '默认分组信息'
// ]

// 使用 create 分组创建用户 只会接受group为create的数据信息
$user2 = User::setGroups(['create'])->from(
    id:1,
    name: '李四',
    score: 100,
    username: 'username',
    email: 'zhangsan@example.com',
    sensitiveData:'机密信息',
    noGroupInfo:'默认分组信息'
);

// 使用 create 分组 toArray
$createArray = $user2->toArray();
// $createArray 的内容:
// [
//     'name' => '李四',
//     'username' => 'username',
//     'email' => 'zhangsan@example.com',
//     'noGroupInfo' => '默认分组信息'
// ]

// 使用 update 分组更新用户 只会接受group为update的数据信息
$user3 = User::setGroups(['update'])->from(
    id:1,
    name: '李四',
    score: 100,
    username: 'username',
    email: 'zhangsan@example.com',
    sensitiveData:'机密信息',
    noGroupInfo:'默认分组信息'
);

// 使用 update 分组 toArray
$updateArray = $user3->toArray();
// $updateArray 的内容:
// [
//     'id' => '1',
//     'name' => '李四',
//     'score' => 100,
//     'noGroupInfo' => '默认分组信息'
// ]

// 使用 detail 和 other 展示用户 会接受group为detail和other的数据信息
$user4 = User::setGroups(['detail','other'])->from(
    id:1,
    name: '李四',
    score: 100,
    username: 'username',
    email: 'zhangsan@example.com',
    sensitiveData:'机密信息',
    noGroupInfo:'默认分组信息'
);

// 使用多个分组 toArray
$multiGroupArray = $user4->toArray();
// $multiGroupArray 的内容:
// [
//     'id' => '1',
//     'name' => '李四',
//     'username' => 'username',
//     'score' => 100,
//     'email' => 'zhangsan@example.com',
//     'sensitiveData' => '机密信息',
//     'noGroupInfo' => '默认分组信息'
// ]
```

##### 嵌套类指定Group类展示

```php
class ComplexUser extends Serialize {
    
    public string $name;
    
    public int $sex;

    public ComplexNestedInfo $info;
}

class ComplexNestedInfo extends Serialize {
    
    #[Groups(ComplexAUser::class)]
    public float $money;

    public string $currency;
}

// ComplexNestedInfo 会隐藏currency
$adminUser = ComplexUser::from(
    name: '张三',
    sex: 1,
    info: [
        'money' => 100.00,
        'currency' => 'CNY'
    ];
);

// 输出数据
$adminUserArray = $adminUser->toArray();
// $adminUserArray 的内容:
// [
//     'name' => '张三',
//     'sex' => 1,
//     'info' => [
//         'money' => 100.00
//     ]
// ]
```

#### 名称映射

##### 基础使用

```php
use Astral\Serialize\Attributes\InputName;
use Astral\Serialize\Attributes\OutputName;
use Astral\Serialize\Serialize;

class User extends Serialize {
    // 输入时使用不同的属性名
    #[InputName('user_name')]
    public string $name;

    // 输出时使用不同的属性名
    #[OutputName('user_id')]
    public int $id;

    // 同时支持输入和输出不同名称
    #[InputName('register_time')]
    #[OutputName('registeredAt')]
    public DateTime $createdAt;
}

// 使用不同名称的输入数据
$user = User::from([
    'user_name' => '张三',       // 映射到 $name
    'id' => 123,                // 保持不变
    'register_time' => '2023-01-01 10:00:00'  // 映射到 $createdAt
]);

// 输出数据
$userArray = $user->toArray();
// $userArray 的内容:
// [
//     'name' => '张三',
//     'user_id' => 123,
//     'registeredAt' => '2023-01-01 10:00:00'
// ]
```

##### 多输入/输出名称处理

```php
use Astral\Serialize\Attributes\InputName;
use Astral\Serialize\Attributes\OutputName;
use Astral\Serialize\Serialize;

class MultiOutputUser extends Serialize {
    // 多个输出名称
    #[OutputName('user_id')]
    #[OutputName('id')]
    #[OutputName('userId')]
    public int $id;

    // 多个输出名称 按照声明顺序取地一个匹配的name
    #[InputName('user_name')]
    #[InputName('other_name')]
    #[InputName('userName')]
    public int $name;

}

// 场景1：使用第一个匹配的输入名称
$user1 = MultiInputUser::from([
    'user_name' => '张三'  // 使用 'user_name'
]);
echo $user1->name;  // 输出 '张三'

// 场景2：使用第二个匹配的输入名称
$user2 = MultiInputUser::from([
    'other_name' => '李四'  // 使用 'other_name'
]);
echo $user2->name;  // 输出 '李四'

// 场景3：使用最后的输入名称
$user3 = MultiInputUser::from([
    'userName' => '王五'  // 使用 'userName'
]);
echo $user3->name;  // 输出 '王五'

// 场景4：传入多个的时候 按照声明顺序取地一个匹配的name
$user4 = MultiInputUser::from([
    'userName' => '王五',
    'other_name' => '李四',
    'user_name' => '张三',
]);
echo $user4->name;  // 输出 '张三'

// 创建用户对象
$user = MultiOutputUser::from([
    'id' => 123,
    'name' => '张三'
]);

// 转换为数组
$userArray = $user->toArray();
// $userArray 的内容:
// [
//     'user_id' => 123,
//     'id' => 123,
//     'userId' => 123,
// ]
```

##### 复杂映射场景

```php
use Astral\Serialize\Serialize;

class ComplexUser extends Serialize {
    // 嵌套对象的名称映射
    #[InputName('user_profile')]
    public UserProfile $profile;

    // 数组元素的名称映射
    #[InputName('user_tags')]
    public array $tags;
}

// 处理复杂的输入结构
$complexUser = ComplexUser::from([
    'user_profile' => [
        'nickname' => '小明',
        'age' => 25
    ],
    'user_tags' => ['developer', 'programmer']
]);

// 转换为标准数组
$complexUserArray = $complexUser->toArray();
// $complexUserArray 的内容:
// [
//     'profile' => [
//         'nickname' => '小明',
//         'age' => 25
//     ],
//     'tags' => ['developer', 'programmer']
// ]
```

##### 命名映射高级用法

```php
use Astral\Serialize\Attributes\InputName;
use Astral\Serialize\Attributes\OutputName;
use Astral\Serialize\Support\Mappers\{
    CamelCaseMapper, 
    SnakeCaseMapper, 
    PascalCaseMapper, 
    KebabCaseMapper
};
use Astral\Serialize\Serialize;

class User extends Serialize {
    // 直接指定映射名称
    #[InputName('user_name')]
    #[OutputName('userName')]
    public string $name;

    // 使用映射器进行风格转换
    #[InputName(CamelCaseMapper::class)]
    #[OutputName(SnakeCaseMapper::class)]
    public int $userId;

    // 支持多个映射和分组
    #[InputName('email', groups: 'profile')]
    #[OutputName('userEmail', groups: 'api')]
    public string $email;
}

// 使用不同的映射策略
$user = User::from([
    'user_name' => '张三',       // 映射到 $name
    'user_id' => 123,           // 使用 CamelCaseMapper 转换
    'email' => 'user@example.com' // 仅在 'profile' 分组生效
]);

// 输出时应用不同的映射
$userArray = $user->toArray(
    inputGroups: ['profile'],   // 仅使用 profile 分组的输入映射
    outputGroups: ['api']       // 仅使用 api 分组的输出映射
);
```
  
##### 全局类映射

```php
use Astral\Serialize\Attributes\InputName;
use Astral\Serialize\Attributes\OutputName;
use Astral\Serialize\Support\Mappers\{
    CamelCaseMapper, 
    SnakeCaseMapper, 
    PascalCaseMapper, 
    KebabCaseMapper
};
use Astral\Serialize\Serialize;

#[InputName(SnakeCaseMapper::class)]
#[OutputName(CamelCaseMapper::class)]
class GlobalMappedUser extends Serialize {
    // 类级别的映射会自动应用到所有属性
    public string $firstName;
    public string $lastName;
    public int $userId;
    public DateTime $registeredAt;
}

// 使用全局映射
$user = GlobalMappedUser::from([
    'first_name' => '张',        // 从蛇形映射到 firstName
    'last_name' => '三',         // 从蛇形映射到 lastName
    'user_id' => 123,            // 从蛇形映射到 userId
    'registered_at' => '2023-01-01' // 从蛇形映射到 registeredAt
]);

// 输出时会转换为驼峰命名
$userArray = $user->toArray();
// $userArray 的内容:
// [
//     'firstName' => '张',
//     'lastName' => '三',
//     'userId' => 123,
//     'registeredAt' => '2023-01-01'
// ]

// 局部属性可以覆盖全局映射
#[InputName(SnakeCaseMapper::class)]
class PartialOverrideUser extends Serialize {
    #[InputName(PascalCaseMapper::class)]
    public string $userName;  // 使用帕斯卡命名映射
    
    public string $userEmail;  // 继续使用类级别的全局映射
}

$partialUser = PartialOverrideUser::from([
    'UserName' => '李四',     // 使用帕斯卡映射
    'user_email' => 'user@example.com' // 使用蛇形映射
]);

$partialUser->toArray();
// $partialUser 的内容:
// [
//     'userName' => '李四',
//     'userEmail' => 'user@example.com',
// ]
```

##### 全局类映射的分组使用

```php
use Astral\Serialize\Attributes\Groups;
use Astral\Serialize\Attributes\InputName;
use Astral\Serialize\Attributes\OutputName;
use Astral\Serialize\Support\Mappers\{
    CamelCaseMapper, 
    SnakeCaseMapper, 
    PascalCaseMapper, 
    KebabCaseMapper
};
use Astral\Serialize\Serialize;


#[InputName(SnakeCaseMapper::class, groups: 'external')]
#[InputName(CamelCaseMapper::class, groups: 'api')]
#[OutputName(PascalCaseMapper::class, groups: ['external','api'])]
class ComplexMappedUser extends Serialize {

    #[Groups('external', 'api')]
    public string $firstName;

    #[Groups('external', 'api')]
    public string $lastName;


    #[InputName('full_name', groups: 'special')]
    #[OutputName('userEmail', groups: 'api')]
    #[Groups('external', 'api')]
    public string $fullName;
}

// 使用admin分组
$complexUser = ComplexMappedUser::setGroup('external')->from(
    first_name :'张',    
    last_name :'三'
    full_name: '张三'
);

$complexUser = $complexUser->toArray();
// $complexUser 的内容:
// [
//     'FirstName' => '张',
//     'LastName' => '三',
//     'FullName' => 张三,
// ]

// 如果熟悉指定了OutputName/InputName 则属性规则优先
// 使用public分组
$complexUser = ComplexMappedUser::setGroup('api')->from(
    first_name :'张',    
    last_name :'三'
    full_name: '张三'
);

$complexUser = $complexUser->toArray();
// $complexUser 的内容:
// [
//     'FirstName' => '张',
//     'LastName' => '三',
//     'userEmail' => 张三,
// ]
```

#### 自定义映射器

```php
// 自定义映射器 需要继承NameMapper 并实现 resolve
class CustomMapper implements NameMapper {
    public function resolve(string $name): string {
        // 实现自定义的命名转换逻辑
        return str_replace('user', 'customer', $name);
    }
}

class AdvancedUser extends Serialize {
    #[InputName(CustomMapper::class)]
    public string $name;
}
```

#### 字段忽略

1. **安全性控制**
   - 防止敏感信息的意外泄露
   - 精细控制数据的输入和输出

2. **数据过滤**
   - 根据不同场景过滤字段
   - 为不同的 API 或用户角色定制数据视图

3. **性能优化**
   - 减少不必要字段的序列化开销
   - 精简数据传输

##### 基础使用

```php
use Astral\Serialize\Attributes\InputIgnore;
use Astral\Serialize\Attributes\OutputIgnore;
use Astral\Serialize\Serialize;

class User extends Serialize {

    public string $name;

    // 输入时忽略的字段
    #[InputIgnore]
    public string $internalId;

    // 输出时忽略的字段
    #[OutputIgnore]
    public string $tempData;
}

// 创建用户对象
$user = User::from([
    'name' => '张三',
    'internalId' => 'secret123',  // 这个字段会被忽略
    'tempData' => 'temporary'     // 这个字段会被忽略
]);

echo  $user->internalId; // 这里会输出 ''

// 转换为数组
$userArray = $user->toArray();
// $userArray 的内容:
// [
//     'name' => '张三',
//     'internalId' => '',
// ]
```

##### 分组忽略

```php
use Astral\Serialize\Attributes\InputIgnore;
use Astral\Serialize\Attributes\OutputIgnore;
use Astral\Serialize\Serialize;

class ComplexUser extends Serialize {
   

    #[Group('admin','public')]
    #[InputIgnore('admin')]
    public string $name;

    #[Group('admin','public')]
    #[OutputIgnore('public')]
    public string $secretKey;

    // 支持分组忽略
    #[InputIgnore('admin')]
    #[OutputIgnore('public')]
    #[Group('admin','public')]
    public string $sensitiveInfo;
}

// 使用admin分组
$complexUser = ComplexUser::setGroups('admin')->from([
    'name' => '张三',
    'secretKey' => 'confidential',
    'sensitiveInfo' => '机密信息'
]);

$complexUser = $complexUser->toArray();
// $complexUser 的内容:
// [
//     'name' => '',
//     'secretKey' => 'confidential',
// ]

// 使用public分组
$complexUser = ComplexUser::setGroups('public')->from([
    'name' => '张三',
    'secretKey' => 'confidential',
    'sensitiveInfo' => '机密信息'
]);

$complexUser = $complexUser->toArray();
// $complexUser 的内容:
// [
//     'name' => '张三',
// ]
```

#### 时间转换

1. 格式灵活性
    支持多种输入和输出时间格式
    可以轻松处理不同地区的日期表示
2. 时区处理
    支持在不同时区间转换
    自动处理时间的时区偏移
3. 类型安全
    自动将字符串转换为 DateTime 对象
    保证类型的一致性和正确性

##### 基础使用

```php
use Astral\Serialize\Attributes\InputDateFormat;
use Astral\Serialize\Attributes\OutputDateFormat;
use Astral\Serialize\Serialize;

class TimeExample extends Serialize {

    // 输入时间格式转换
    #[InputDateFormat('Y-m-d')]
    public DateTime $dateTime;

    // 输入时间格式转换
    #[InputDateFormat('Y-m-d')]
    public string $dateDateString;

    // 输出时间格式转换
    #[OutputDateFormat('Y/m/d H:i')]
    public DateTime|string $processedAt;
    

    // 支持多种时间格式
    #[InputDateFormat('Y-m-d H:i:s')]
    #[OutputDateFormat('Y-m-d')]
    public string $createdAt;
}

// 创建订单对象
$order = Order::from([
    'dateTime' => new DateTime('2023-08-11'),           // 输入格式：Y-m-d
    'dateDateString' => '2023-08-15',           // 输入格式：Y-m-d
    'processedAt' => '2023-08-16 14:30',   // 输入默认格式 也支持DateTime对象
    'createdAt' => '2023-08-16 14:45:30'   // 输入格式：Y-m-d H:i:s
]);

// 转换为数组
$orderArray = $order->toArray();
// $orderArray 的内容:
// [
//     'dateTime' => '2023-08-11',
//     ’dateDateString' => '2023-08-15',
//     'processedAt' => '2023/08/16 14:30',
//     'createdAt' => '2023-08-16'
// ]
```

##### 带时区的时间转换

```php
class AdvancedTimeUser extends Serialize {
    // 支持时区转换
    #[InputDateFormat('Y-m-d H:i:s', timezone: 'UTC')]
    #[OutputDateFormat('Y-m-d H:i:s', timezone: 'Asia/Shanghai')]
    public DateTime $registeredAt;

    // 支持不同地区的时间格式
    #[InputDateFormat('d/m/Y')]  // 英国格式
    #[OutputDateFormat('Y-m-d')]  // 标准格式
    public DateTime $birthDate;
}

// 使用高级时间转换
$advancedUser = AdvancedTimeUser::from([
    'registeredAt' => '2023-08-16 10:00:00',  // UTC 时间
    'birthDate' => '15/08/1990'  // 英国日期格式
]);

$advancedUserArray = $advancedUser->toArray();
// $advancedUserArray 的内容:
// [
//     'registeredAt' => '2023-08-16 18:00:00',  // 转换为上海时区
//     'birthDate' => '1990-08-15'
// ]
```

### 高级类型处理

#### 联合类型

```php
class FlexibleData extends Serialize {
    public int|string $flexibleId;
    public User|int $userIdentifier;
}
```

#### 多对象处理

```php
class MultiObjectSerialize extends Serialize {
    /** @var ArrayBestMatchOne|ArrayBestMatchTwo|ArrayBestMatchThree */
    public object $singleObject;

    /** @var (ArrayBestMatchOne|ArrayBestMatchTwo)[] */
    public array $mixedArray;
}
```

## Faker

### 简单属性模拟

```php
class UserFaker extends Serialize {
    #[FakerValue('name')]
    public string $name;

    #[FakerValue('email')]
    public string $email;
}

$user = UserFaker::faker();
```

### 集合模拟

```php
class UserListFaker extends Serialize {
    #[FakerCollection(['name', 'email'], num: 3)]
    public array $users;
}

$userList = UserListFaker::faker();
```

### 嵌套对象模拟

```php
class ComplexUserFaker extends Serialize {
    #[FakerObject(UserProfile::class)]
    public UserProfile $profile;
}
```

### Faker 方法

```php
class UserService {
    public function generateUserData(): array {
        return ['name' => 'Generated User'];
    }
}

class UserFaker extends Serialize {
    #[FakerMethod(UserService::class, 'generateUserData')]
    public array $userData;
}
```