## Faker类方法模拟

### 基本用法

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

### 完整的示例

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