## 属性分组（Groups）

属性分组提供了一种灵活的方式来控制属性的输入和输出行为，允许在不同场景下精细地管理数据转换。

---

### 分组原理说明

- 使用 `#[Groups(...)]` 注解可将属性归类到一个或多个分组中。
- 支持：
    - **输入时** 按分组过滤数据字段
    - **输出时** 按分组筛选输出字段
- 未指定分组的属性将自动归入 `"default"` 分组。

---

### 基本示例

```php
use Astral\Serialize\Attributes\Groups;
use Astral\Serialize\Serialize;

class User extends Serialize {

    #[Groups('update', 'detail')]
    public string $id;

    #[Groups('create', 'update', 'detail')]
    public string $name;

    #[Groups('create', 'detail')]
    public string $username;

    #[Groups('other')]
    public string $sensitiveData;

    // 未指定分组，默认为 default 分组
    public string $noGroupInfo;

    public function __construct(
        #[Groups('create', 'detail')]
        public readonly string $email,

        #[Groups('update', 'detail')]
        public readonly int $score
    ) {}
}
```

### 按分组接收

```php
// 使用 create 分组创建用户，只接受 group=create 的字段
$user = User::setGroups(['create'])->from([
    'id' => 1,
    'name' => '李四',
    'score' => 100,
    'username' => 'username',
    'email' => 'zhangsan@example.com',
    'sensitiveData' => '机密信息',
    'noGroupInfo' => '默认信息'
]);

$user->toArray();
/*
[
    'name' => '李四',
    'username' => 'username',
    'email' => 'zhangsan@example.com',
]
*/
```

### 按分组输出

```php
$user = User::from([
    'id' => 1,
    'name' => '李四',
    'score' => 100,
    'username' => 'username',
    'email' => 'zhangsan@example.com',
    'sensitiveData' => '机密信息',
    'noGroupInfo' => '默认信息'
]);

// 默认输出所有字段
$user->toArray();
/*
[
    'id' => '1',
    'name' => '李四',
    'username' => 'username',
    'score' => 100,
    'email' => 'zhangsan@example.com',
    'sensitiveData' => '机密信息',
    'noGroupInfo' => '默认信息'
]
*/

// 指定输出分组
$user->withGroups('create')->toArray();
/*
[
    'name' => '李四',
    'username' => 'username',
    'email' => 'zhangsan@example.com',
]
*/

$user->withGroups(['detail', 'other'])->toArray();
/*
[
    'id' => '1',
    'name' => '李四',
    'username' => 'username',
    'score' => 100,
    'email' => 'zhangsan@example.com',
    'sensitiveData' => '机密信息',
]
*/
```

### 嵌套对象的分组

```php
class ComplexUser extends Serialize {
    public string $name;
    public int $sex;
    public ComplexNestedInfo $info;
}

class ComplexNestedInfo extends Serialize {
    #[Groups(ComplexUser::class)]
    public float $money;

    public string $currency;
}

$adminUser = ComplexUser::from([
    'name' => '张三',
    'sex' => 1,
    'info' => [
        'money' => 100.00,
        'currency' => 'CNY'
    ],
]);

// info只会输出$money
// 因为 ComplexNestedInfo 绑定了 ComplexUser的类Group
$adminUser->toArray();
/*
[
    'name' => '张三',
    'sex' => 1,
    'info' => [
        'money' => 100.00
    ]
]
*/
```