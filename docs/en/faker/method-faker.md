## Faker Class Method Simulation

### Basic Usage

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

### Demonstration Example

```php
use Astral\Serialize\Serialize;
use Astral\Serialize\Attributes\Faker\FakerMethod;
use Astral\Serialize\Attributes\Faker\FakerObject;
use Astral\Serialize\Attributes\Faker\FakerCollection;

// User profile class
class UserProfile extends Serialize {
    public string $nickname;
    public int $age;
    public string $email;
    public array $types = ['type1' => 'money', 'type2' => 'score'];
}

// User service class, provides data generation methods
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

// Faker method simulation example
class UserFaker extends Serialize {
    // Use method to generate simple data
    #[FakerMethod(UserService::class, 'generateUserData')]
    public array $userData;

    // Use method to generate object
    #[FakerMethod(UserService::class, 'generateUserProfile')]
    public UserProfile $userProfile;

    // 获取指定属性
    #[FakerMethod(UserService::class, 'generateUserProfile',returnType:'age')]
    public int $age;

    // Get specified property, use [.] for multiple levels
    #[FakerMethod(UserService::class, 'generateUserProfile',returnType:'types.type2')]
    public string $type2;

    // Pass parameters
    #[FakerMethod(UserService::class, 'generateUserList',params:['count'=> 3])]
    public array $userList;
}

// Generate fake data
$userFaker = UserFaker::faker();

// Convert to array
$userFakerArray = $userFaker->toArray();
// Content of $userFakerArray:
// [
//     'userData' => [
//         'name' => 'Generated User',
//         'email' => 'generated.user@example.com',
//         'age' => 30
//     ],
//     'userProfile' => UserProfile Object (
//         [
//             'nickname' => 'GeneratedNickname', 
//             'age' => 25, // Randomly generated
//             'email' => 'profile@example.com'
//             'types' => ['type1' => 'money', 'type2' => 'score']
//         ]
//     ),
//     'age' => 99 , // rand 
//     'type2' => 'score',
//     'userList' => [
//         ['name' => 'User 0', 'email' => 'user0@example.com'],
//         ['name' => 'User 1', 'email' => 'user1@example.com'],
//         ['name' => 'User 2', 'email' => 'user2@example.com']
//     ]
// ]
```