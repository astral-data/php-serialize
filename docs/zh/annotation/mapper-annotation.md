## Mapper映射

### 属性映射

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

### 全局类映射

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

### 全局类映射的分组使用

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
### 自定义映射器

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

### Tips：属性映射优先于类级映射

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