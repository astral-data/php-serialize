## Nested Object Faker

### Basic Usage

```php
class ComplexUserFaker extends Serialize {
    #[FakerObject(UserProfile::class)]
    public UserProfile $profile;
}
```

### Demonstration Example

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
// Content of $complexUserFakerArray:
// [
//     'profile' => UserProfile Object (
//         ['nickname' => 'RandomNickname', 'age' => 28, 'email' => 'random.user@example.com', 'avatar' => 'https://example.com/avatars/random-avatar.jpg']
//     ),
//     'primaryTag' => UserTag Object (
//         ['name' => 'Developer', 'color' => '#007bff']
//     )
// ]
```
