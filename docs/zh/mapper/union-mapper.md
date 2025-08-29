## 联合类型

1. 可以混合使用基本类型和对象类型
2. 对象层级匹配。对于多个对象类型，会选择最匹配的类型。支持继承层级的智能匹配
3. 动态类型处理，自动处理不同类型的，输入提供更加灵活的数据建模方式

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

var_dump($data2)
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

echo $data3->userIdentifier; // 输出 User 对象
echo $data3->complexIdentifier; // 输出 AdminUser 对象

var_dump($data3)
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