## 名称映射

### 基础使用

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

### 多输入/输出名称处理

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

### 复杂映射场景

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