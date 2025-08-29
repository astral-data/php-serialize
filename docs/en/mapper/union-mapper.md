## Union Types

1. Can mix basic types and object types
2. Object hierarchy matching. For multiple object types, the best match will be selected. Supports smart matching for inheritance hierarchies.
3. Dynamic type handling, automatically processes different types, providing more flexible data modeling for input.

```php
use Astral\Serialize\Serialize;

// Define a base user class
class User extends Serialize {
    public string $name;
    public int $age;
}

// Define an admin user class
class AdminUser extends User {
    public string $role;
}

class FlexibleData extends Serialize {
    // Supports integer or string type identifier
    public int|string $flexibleId;

    // Supports user object or integer identifier
    public User|int $userIdentifier;

    // Supports multiple complex union types
    public AdminUser|User|int $complexIdentifier;
}

// Scenario 1: Use integer as flexibleId
$data1 = FlexibleData::from([
    'flexibleId' => 123,
    'userIdentifier' => 456,
    'complexIdentifier' => 789
]);

$data1Array = $data1->toArray();
// Content of $data1Array:
// [
//     'flexibleId' => 123,
//     'userIdentifier' => 456,
//     'complexIdentifier' => 789
// ]

// Scenario 2: Use string as flexibleId
$data2 = FlexibleData::from([
    'flexibleId' => 'ABC123',
    'userIdentifier' => [
        'name' => 'Tom',
        'age' => 30
    ],
    'complexIdentifier' => [
        'name' => 'Job',
        'age' => 25
    ]
]);

echo $data2->userIdentifier; // Output User object
echo $data2->complexIdentifier; // Output AdminUser object

var_dump($data2)
// Content of $data2:
// [
//     'flexibleId' => 'ABC123',
//     'userIdentifier' => User Object (
//         ['name' => 'Tom', 'age' => 30]
//     ),
//     'complexIdentifier' => User Object (
//         ['name' => 'Job', 'age' => 25]
//     )
// ]

// Scenario 3: Use admin user
$data3 = FlexibleData::from([
    'flexibleId' => 'USER001',
    'userIdentifier' => [
        'name' => 'Tom',
        'age' => 35,
        'role' => 'admin'
    ],
    'complexIdentifier' => [
        'name' => 'Job',
        'age' => 40,
        'role' => 'super_admin'
    ]
]);

echo $data3->userIdentifier; // Output User object
echo $data3->complexIdentifier; // Output AdminUser object

var_dump($data3)
// Content of $data3:
// [
//     'flexibleId' => 'USER001',
//     'userIdentifier' => User Object (
//         ['name' => 'Tom', 'age' => 35]
//     ),
//     'complexIdentifier' => AdminUser Object (
//         ['name' => 'Job', 'age' => 40, 'role' => 'super_admin']
//     )
// ]
```