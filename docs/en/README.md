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

## 生成openapi文档

#### 创建Request文件
```php
use Astral\Serialize\Serialize;

class UserAddRequest extends Serialize {
    public string $name;
    public int $id;
}

class UserDetailRequest extends Serialize {
    public int $id;
}
```

#### 创建Repose文件
```php
use Astral\Serialize\Serialize;

class UserDto extends Serialize {
    public string $name,
    public int $id;
}
```

#### 创建Controller文件
```php
use Astral\Serialize\Serialize;
use Astral\Serialize\OpenApi\Enum\MethodEnum;

#[\Astral\Serialize\OpenApi\Annotations\Tag('用户模块管理')]
class UserController {

    #[\Astral\Serialize\OpenApi\Annotations\Summary('创建用户')]
    #[\Astral\Serialize\OpenApi\Annotations\Route('/user/create')]
    #[\Astral\Serialize\OpenApi\Annotations\RequestBody(UserAddRequest::class)]
     #[\Astral\Serialize\OpenApi\Annotations\Response(UserDto::class)]
    public function create() 
    {
        return new UserDto(); 
    }
    
    #[\Astral\Serialize\OpenApi\Annotations\Summary('用户详情')]
    #[\Astral\Serialize\OpenApi\Annotations\Route(route:'/user/detail', method: MethodEnum::GET)]
    public function detail(UserDetailRequest $request): UserDto  
    {
        return new UserDto();
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

var_dump($user->status); // 输出: UserStatus::ACTIVE

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

#### 联合类型

1. 可以混合使用基本类型和对象类型
2. 对象层级匹配
    对于多个对象类型，会选择最匹配的类型
    支持继承层级的智能匹配
3. 动态类型处理
    自动处理不同类型的输入
    提供更加灵活的数据建模方式

```php
use Astral\Serialize\Serialize;

// 定义一个基础用户类
class User extends Serialize {
    public string $name;
    public int $age;
}

// 定义一个管理员用户类
class AdminUser extends User {
    public string $role;
}

class FlexibleData extends Serialize {
    // 支持整数或字符串类型的标识符
    public int|string $flexibleId;

    // 支持用户对象或整数标识符
    public User|int $userIdentifier;

    // 支持多种复杂的联合类型
    public AdminUser|User|int $complexIdentifier;
}

// 场景1：使用整数作为 flexibleId
$data1 = FlexibleData::from([
    'flexibleId' => 123,
    'userIdentifier' => 456,
    'complexIdentifier' => 789
]);

$data1Array = $data1->toArray();
// $data1Array 的内容:
// [
//     'flexibleId' => 123,
//     'userIdentifier' => 456,
//     'complexIdentifier' => 789
// ]

// 场景2：使用字符串作为 flexibleId
$data2 = FlexibleData::from([
    'flexibleId' => 'ABC123',
    'userIdentifier' => [
        'name' => '张三',
        'age' => 30
    ],
    'complexIdentifier' => [
        'name' => '李四',
        'age' => 25
    ]
]);

echo $data2->userIdentifier; // 输出 User 对象
echo $data2->complexIdentifier; // 输出 User 对象

$data2Array = $data2->toArray();
// $data2Array 的内容:
// [
//     'flexibleId' => 'ABC123',
//     'userIdentifier' => User Object (
//         ['name' => '张三', 'age' => 30]
//     ),
//     'complexIdentifier' => User Object (
//         ['name' => '李四', 'age' => 25]
//     )
// ]

// 场景3：使用管理员用户
$data3 = FlexibleData::from([
    'flexibleId' => 'USER001',
    'userIdentifier' => [
        'name' => '王五',
        'age' => 35,
        'role' => 'admin'
    ],
    'complexIdentifier' => [
        'name' => '赵六',
        'age' => 40,
        'role' => 'super_admin'
    ]
]);

echo $data2->userIdentifier; // 输出 User 对象
echo $data2->complexIdentifier; // 输出 AdminUser 对象

$data3Array = $data3->toArray();
// $data3Array 的内容:
// [
//     'flexibleId' => 'USER001',
//     'userIdentifier' => User Object (
//         ['name' => '王五', 'age' => 35]
//     ),
//     'complexIdentifier' => AdminUser Object (
//         ['name' => '赵六', 'age' => 40, 'role' => 'super_admin']
//     )
// ]
```

#### 数组对象转换

##### phpDoc定义

```php
use Astral\Serialize\Serialize;

// 定义基础数组类型
class ArrayOne extends Serialize {
    public string $type = 'one';
    public string $name;
}

class ArrayTwo extends Serialize {
    public string $type = 'two';
    public string $code;
}

class MultiArraySerialize extends Serialize {
    // 场景1：混合类型数组
    /** @var (ArrayOne|ArrayTwo)[] */
    public array $mixedTypeArray;

    // 场景2：多类型数组
    /** @var ArrayOne[]|ArrayTwo[] */
    public array $multiTypeArray;

    // 场景3：键值对混合类型
    /** @var array(string, ArrayOne|ArrayTwo) */
    public array $keyValueMixedArray;
}

// 场景1：混合类型数组
$data1 = MultiArraySerialize::from(
    mixedTypeArray : [
        ['name' => '张三'],           //  转化 ArrayOne 对象
        ['code' => 'ABC123'],         // 转化 ArrayTwo 对象
        ['name' => '李四'],            // 转化 ArrayOne 对象
        ['code' => 'DEF456']          // 转化 ArrayTwo 对象
    ]
);

$data1Array = $data1->toArray();
// $data1Array 的内容:
// [
//     'mixedTypeArray' => [
//           [0] => ArrayOne Object
//                (
//                    ['name' => '张三', 'type' => 'one'],
//                )
//           [1] => ArrayTwo Object
//                (
//                    ['code' => 'ABC123', 'type' => 'two'],
//                )
//           [2] => ArrayOne Object
//                (
//                    ['name' => '李四', 'type' => 'one'],
//                )
//           [3] => ArrayTwo Object
//                (
//                    ['code' => 'DEF456', 'type' => 'two'],
//                )
//     ]
// ]

// 场景2：多类型数组
$data2 = MultiArraySerialize::from(
    multiTypeArray:[
        ['name' => '王五'],            // 转化 ArrayOne 对象
        ['name' => '赵六'],            // 转化 ArrayOne 对象
        ['code' => 'GHI789']          // 转化 ArrayTwo 对象
    ]
);

$data2Array = $data2->toArray();
// $data2Array 的内容:
// [
//     'multiTypeArray' => [
//         ArrayOne Object (
//             ['name' => '王五', 'type' => 'one']
//         ),
//         ArrayOne Object (
//             ['name' => '赵六', 'type' => 'one']
//         ),
//         ArrayTwo Object (
//             ['code' => 'GHI789', 'type' => 'two']
//         )
//     ]
// ]

// 场景3：键值对混合类型
$data3 = MultiArraySerialize::from(
    keyValueMixedArray: [
        'user1' => ['name' => '张三'],           // 转化 ArrayOne 对象
        'system1' => ['code' => 'ABC123'],       // 转化 ArrayTwo 对象
        'user2' => ['name' => '李四']            // 转化 ArrayOne 对象
    ]
);

$data3Array = $data3->toArray();
// $data3Array 的内容:
// [
//     'keyValueMixedArray' => [
//         'user1' => ArrayOne Object (
//             ['name' => '张三', 'type' => 'one']
//         ),
//         'system1' => ArrayTwo Object (
//             ['code' => 'ABC123', 'type' => 'two']
//         ),
//         'user2' => ArrayOne Object (
//             ['name' => '李四', 'type' => 'one']
//         )
//     ]
// ]

// 场景4：无法匹配时的处理
$data4 = MultiArraySerialize::from(
    mixedTypeArray : [
        ['unknown' => 'data1'],
        ['another' => 'data2']
    ]
);

$data4Array = $data4->toArray();
// $data4Array 的内容:
// [
//     'mixedTypeArray' => [
//         ['unknown' => 'data1'],
//         ['another' => 'data2']
//     ]
// ]
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

// 指定分组内容输入
$defaultArray = $user1->withGroups('create')->toArray();
// 输出内容
// [
//     'name' => '李四',
//     'username' => 'username',
//     'email' => 'zhangsan@example.com',
// ]

$defaultArray = $user1->withGroups(['detail','other'])->toArray();
// 输出内容
// [
//     'id' => '1',
//     'name' => '李四',
//     'username' => 'username',
//     'score' => 100,
//     'email' => 'zhangsan@example.com',
//     'sensitiveData' => '机密信息',
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

// ComplexNestedInfo 会自动隐藏currency
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
//     'info' => ComplexNestedInfo Object ([
//         'money' => 100.00
//     ])
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
// tips: 因为id 有多个outputname 所以输出了 ['user_id','id','userId']
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
//     'profile' => UserProfile Object ([
//         'nickname' => '小明',
//         'age' => 25
//     ]),
//     'tags' => ['developer', 'programmer']
// ]
```

##### Mapper映射

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

#[Groups('profile','api')]
class User extends Serialize {
    // 直接指定映射名称
    #[InputName('user_name', groups: ['profile','api'])]
    #[OutputName('userName', groups: ['profile','api'])]
    public string $name;

    // 使用映射器进行风格转换
    #[InputName(CamelCaseMapper::class, groups: ['profile','api'])]
    #[OutputName(SnakeCaseMapper::class, groups: ['profile','api'])]
    public int $userId;

    // 支持多个映射和分组
    #[InputName('profile-email', groups: 'profile')]
    #[OutputName('userEmail', groups: 'profile')]
    public string $email;
}

// 使用不同的映射策略
$user = User::setGroups('profile')::from([
    'user_name' => '张三',       // 映射到 $name
    'userId' => 123,           // 使用 CamelCaseMapper 转换
    'profile-email' => 'user@example.com' // 仅在 'profile' 分组生效
]);

// 输出时应用不同的映射
$userArray = $user->toArray();
// $userArray 的内容:
// [
//     'userName' => '张三',
//     'user_id' => '三',
//     'userEmail' => user@example.com,
// ]
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
```

###### 属性映射大于类级映射

```php

#[InputName(SnakeCaseMapper::class)]
class PartialOverrideUser extends Serialize {
    #[InputName(PascalCaseMapper::class)]
    public string $userName;  // 优先使用帕斯卡命名映射
    
    public string $userEmail;  // 继续使用类级别的全局映射
}

$partialUser = PartialOverrideUser::from([
    'User_name' => '张三',     // 使用蛇形映射
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

###### 全局类映射的分组使用

需要搭配`Groups`注解一起使用

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

忽略分组需要搭配Groups注解一起使用

```php
use Astral\Serialize\Attributes\Input\InputIgnore;
use Astral\Serialize\Attributes\Output\OutputIgnore;
use Astral\Serialize\Serialize;
use Astral\Serialize\Attributes\Groups;

class ComplexUser extends Serialize {
   
    #[Groups('admin','public')]
    #[InputIgnore('admin')]
    public string $name;

    #[Groups('admin','public')]
    #[OutputIgnore('public')]
    public string $secretKey;

    #[Groups('admin','public')]
    #[InputIgnore('admin')]
    #[OutputIgnore('public')]
    public string $sensitiveInfo;

    #[InputIgnore]
    public string $globalInputIgnore;

     #[OutputIgnore]
    public string $globalOutputIgnore;
}

// 默认分组
$complexUser = ComplexUser::from([
    'name' => '张三',
    'secretKey' => 'confidential',
    'sensitiveInfo' => '机密信息',
    'globalInputIgnore' => '全局输入忽略',
    'globalOutputIgnore' => '全局输出忽略'
]);

echo $complexUser->globalInputIgnore; // 输出 ‘’
echo $complexUser->globalOutputIgnore; // 输出 ‘全局输出忽略’

$complexUser = $complexUser->toArray();
// $complexUser 的内容:
// [
//    'name' => '张三',
//    'secretKey' => 'confidential',
//    'sensitiveInfo' => '机密信息',
//    'globalInputIgnore' => '',
// ]


// 使用admin分组
$complexUser = ComplexUser::setGroups('admin')->from([
    'name' => '张三',
    'secretKey' => 'confidential',
    'sensitiveInfo' => '机密信息'
    'globalInputIgnore' => '全局输入忽略',
    'globalOutputIgnore' => '全局输出忽略'
]);

$complexUser = $complexUser->toArray();
// $complexUser 的内容:
// [
//     'name' => '',
//     'secretKey' => 'confidential',
// 'globalInputIgnore' => '',
// ]

// 使用public分组
$complexUser = ComplexUser::setGroups('public')->from([
    'name' => '张三',
    'secretKey' => 'confidential',
    'sensitiveInfo' => '机密信息'
    'globalInputIgnore' => '全局输入忽略',
    'globalOutputIgnore' => '全局输出忽略'
]);

$complexUser = $complexUser->toArray();
// $complexUser 的内容:
// [
//     'name' => '张三',
///    'globalInputIgnore' => '',
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

    // 输出时间格式转换
    #[OutputDateFormat('Y/m/d H:i')]
    public DateTime|string $processedAt;
    

    // 支持多种时间格式
    #[InputDateFormat('Y-m-d H:i:s')]
    #[OutputDateFormat('Y-m-d')]
    public string $createdAt;
}

// 创建订单对象
$order = TimeExample::from([
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

use Astral\Serialize\Attributes\Input\InputDateFormat;
use Astral\Serialize\Attributes\Output\OutputDateFormat;
use Astral\Serialize\Serialize;

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

## Faker

### 简单属性模拟

```php
class UserFaker extends Serialize {
    #[FakerValue('name')]
    public string $name;

    #[FakerValue('email')]
    public string $email;

    #[FakerValue('uuid')]
    public string $userId;

    #[FakerValue('phoneNumber')]
    public string $phone;

    #[FakerValue('age')]
    public int $age;

    #[FakerValue('boolean')]
    public bool $isActive;
}

$user = UserFaker::faker();

$userArray = $user->toArray();
// $userArray 的内容:
// [
//    "name" => "John Doe"
//    "email" => "john.doe@example.com"
//    "userId" => "550e8400-e29b-41d4-a716-446655440000"
//    "phone" => "+1-555-123-4567"
//    "age" => 35
//    "isActive" => true
// ]
```

### 集合模拟

```php

class UserProfile extends Serialize {
    public string $nickname;
    public int $age;
    public string $email;
    public string $avatar;
}

class UserListFaker extends Serialize {
    #[FakerCollection(['name', 'email'], num: 3)]
    public array $users;

     #[FakerCollection(UserProfile::class, num: 2)]
    public array $profiles;
}

$userList = UserListFaker::faker();

$complexUserListFaker = UserListFaker::faker();

$complexUserListFakerArray = $complexUserListFaker->toArray();
// $complexUserListFakerArray 的内容:
// [
//     'profile' => [
//        [0] => UserProfile Object (
//              [
//              'nickname' => 'RandomNickname', 
//              'age' => 28, 'email' => 'random.user@example.com', 
//              'avatar' => 'https://example.com/avatars/random-avatar.jpg'
//              ],
//         ),
//         [1] => UserProfile Object (
//              [
//              'nickname' => 'RandomNickname', 
//              'age' => 28, 'email' => 'random.user@example.com', 
//              'avatar' => 'https://example.com/avatars/random-avatar.jpg'
//              ],
//         )
//      ],  
//     'users' => [
//         ['name' => 'RandomNickname', 'email' => 'RandomEmail@example.com']
//         ['name' => 'RandomNickname', 'email' => 'RandomEmail@example.com']
//         ['name' => 'RandomNickname', 'email' => 'RandomEmail@example.com']
//     ]
// ]
```

### 嵌套对象模拟

#### 基本用法

```php
class ComplexUserFaker extends Serialize {
    #[FakerObject(UserProfile::class)]
    public UserProfile $profile;
}
```

#### 演示实例

```php
use Astral\Serialize\Serialize;
use Astral\Serialize\Attributes\FakerObject;
use Astral\Serialize\Attributes\FakerCollection;

class UserProfile extends Serialize {
    public string $nickname;
    public int $age;
    public string $email;
    public string $avatar;
}

class UserTag extends Serialize {
    public string $name;
    public string $color;
}

class ComplexUserFaker extends Serialize {
    #[FakerObject(UserProfile::class)]
    public UserProfile $profile;

    #[FakerObject(UserTag::class)]
    public UserTag|UserProfile $primaryTag;

}

$complexUserFaker = ComplexUserFaker::faker();

$complexUserFakerArray = $complexUserFaker->toArray();
// $complexUserFakerArray 的内容:
// [
//     'profile' => UserProfile Object (
//         ['nickname' => 'RandomNickname', 'age' => 28, 'email' => 'random.user@example.com', 'avatar' => 'https://example.com/avatars/random-avatar.jpg']
//     ),
//     'primaryTag' => UserTag Object (
//         ['name' => 'Developer', 'color' => '#007bff']
//     )
// ]
```

### Faker类方法模拟

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

#### 完整的示例

```php
use Astral\Serialize\Serialize;
use Astral\Serialize\Attributes\Faker\FakerMethod;
use Astral\Serialize\Attributes\Faker\FakerObject;
use Astral\Serialize\Attributes\Faker\FakerCollection;

// 用户配置文件类
class UserProfile extends Serialize {
    public string $nickname;
    public int $age;
    public string $email;
    public array $types = ['type1' => 'money', 'type2' => 'score'];
}

// 用户服务类，提供数据生成方法
class UserService {
    public function generateUserData(): array {
        return [
            'name' => 'Generated User',
            'email' => 'generated.user@example.com',
            'age' => 30
        ];
    }

    public function generateUserProfile(UserProfile $user): UserProfile {
        return $user;
    }

    public function generateUserList(int $count): array {
        $users = [];
        for ($i = 0; $i < $count; $i++) {
            $users[] = [
                'name' => "User {$i}",
                'email' => "user{$i}@example.com"
            ];
        }
        return $users;
    }
}

// Faker 方法模拟示例
class UserFaker extends Serialize {
    // 使用方法生成简单数据
    #[FakerMethod(UserService::class, 'generateUserData')]
    public array $userData;

    // 使用方法生成对象
    #[FakerMethod(UserService::class, 'generateUserProfile')]
    public UserProfile $userProfile;

    // 获取指定属性
    #[FakerMethod(UserService::class, 'generateUserProfile',returnType:'age')]
    public int $age;

    // 获取指定属性 多级可以使用[.]链接
    #[FakerMethod(UserService::class, 'generateUserProfile',returnType:'types.type2')]
    public string $type2;

    // 传入参数
    #[FakerMethod(UserService::class, 'generateUserList',params:['count'=> 3])]
    public array $userList;
}

// 生成模拟数据
$userFaker = UserFaker::faker();

// 转换为数组
$userFakerArray = $userFaker->toArray();
// $userFakerArray 的内容:
// [
//     'userData' => [
//         'name' => 'Generated User',
//         'email' => 'generated.user@example.com',
//         'age' => 30
//     ],
//     'userProfile' => UserProfile Object (
//         [
//             'nickname' => 'GeneratedNickname', 
//             'age' => 25, // 随机生成
//             'email' => 'profile@example.com'
//             'types' => ['type1' => 'money', 'type2' => 'score']
//         ]
//     ),
//     'age' => 99 , // 随机生成
//     'type2' => 'score',
//     'userList' => [
//         ['name' => 'User 0', 'email' => 'user0@example.com'],
//         ['name' => 'User 1', 'email' => 'user1@example.com'],
//         ['name' => 'User 2', 'email' => 'user2@example.com']
//     ]
// ]
```
