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