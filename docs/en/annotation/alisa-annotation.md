## Name Mapping

### Basic Usage

```php
use Astral\Serialize\Attributes\InputName;
use Astral\Serialize\Attributes\OutputName;
use Astral\Serialize\Serialize;

class User extends Serialize {
    // Use a different property name for input
    #[InputName('user_name')]
    public string $name;

    // Use a different property name for output
    #[OutputName('user_id')]
    public int $id;

    // Support different names for both input and output
    #[InputName('register_time')]
    #[OutputName('registeredAt')]
    public DateTime $createdAt;
}

// Input data with different names
$user = User::from([
    'user_name' => 'Job',       // Mapped to $name
    'id' => 123,                // Remains unchanged
    'register_time' => '2023-01-01 10:00:00'  // Mapped to $createdAt
]);

// Output data
$userArray = $user->toArray();
// $userArray toArray:
// [
//     'name' => 'Job',
//     'user_id' => 123,
//     'registeredAt' => '2023-01-01 10:00:00'
// ]
```

### Handling Multiple Input/Output Names

```php
use Astral\Serialize\Attributes\InputName;
use Astral\Serialize\Attributes\OutputName;
use Astral\Serialize\Serialize;

class MultiOutputUser extends Serialize {
    // output multiple names
    #[OutputName('user_id')]
    #[OutputName('id')]
    #[OutputName('userId')]
    public int $id;

    // Multiple input names, the first matching name in declaration order will be used
    #[InputName('user_name')]
    #[InputName('other_name')]
    #[InputName('userName')]
    public int $name;

}

// Scenario 1: Use the first matching input name
$user1 = MultiInputUser::from([
    'user_name' => 'Job'  // Use 'user_name'
]);
echo $user1->name;  // Output 'Job'

// Scenario 2: Use the second matching input name
$user2 = MultiInputUser::from([
    'other_name' => 'Tom'  // Use 'Tom'
]);
echo $user2->name;  // Output 'Tom'

// Scenario 3: Use the last input name
$user3 = MultiInputUser::from([
    'userName' => 'Lin'  // Use 'userName'
]);
echo $user3->name;  // Output 'Lin'

// Scenario 4: When multiple are passed, the first matching name in declaration order is used
$user4 = MultiInputUser::from([
    'userName' => 'Job',
    'other_name' => 'Tom',
    'user_name' => 'Lin',
]);
echo $user4->name;  // Output 'Job'

// Create user object
$user = MultiOutputUser::from([
    'id' => 123,
    'name' => 'Job'
]);

// Convert to array
// tips: Since id has multiple output names, the output includes ['user_id','id','userId']
$userArray = $user->toArray();
// $userArray toArray:
// [
//     'user_id' => 123,
//     'id' => 123,
//     'userId' => 123,
// ]
```

### Complex Mapping Scenarios

```php
use Astral\Serialize\Serialize;

class ComplexUser extends Serialize {
    // Name mapping for nested objects
    #[InputName('user_profile')]
    public UserProfile $profile;

    // Name mapping for array elements
    #[InputName('user_tags')]
    public array $tags;
}

// Handle complex input structure
$complexUser = ComplexUser::from([
    'user_profile' => [
        'nickname' => 'job',
        'age' => 25
    ],
    'user_tags' => ['developer', 'programmer']
]);

var_dump($complexUserListFaker)
// Content of $complexUserListFaker:
// [
//     'profile' => UserProfile Object ([
//         'nickname' => 'job',
//         'age' => 25
//     ]),
//     'tags' => ['developer', 'programmer']
// ]
```