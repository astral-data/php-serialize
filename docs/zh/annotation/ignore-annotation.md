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