## 属性分组

属性分组提供了一种灵活的方式来控制属性的输入和输出行为，允许在不同场景下精细地管理数据转换。

### 基本用法

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

### 嵌套类指定Group类展示

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