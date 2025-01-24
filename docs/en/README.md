# Astral Serialize Documentation

## Quick Start

### Installation

Install using Composer:

```bash
composer require astral/serialize
```

### Basic Usage

```php
use Astral\Serialize\Serialize;

class User extends Serialize {
    public string $name,
    public int $age
}

// Create object from array
$user = User::from([
    'name' => 'John Doe',
    'age' => 30
]);

// Access object properties
echo $user->name;  // Output: John Doe
echo $user->age;   // Output: 30

// Convert to array
$userArray = $user->toArray();
// $userArray contents:
// [
//     'name' => 'John Doe',
//     'age' => 30
// ]
```

#### Other Features

1. **Immutability**: Read-only properties cannot be modified after construction

```php
use Astral\Serialize\Serialize;

class User extends Serialize {
    public function __construct(
        public readonly string $name,
        public readonly int $age
    ) {}
}

$user = User::from([
    'name' => 'John Doe',
    'age' => 30
]);

try {
    $user->name = 'Jane Doe';  // Compile-time error: cannot modify read-only property
} catch (Error $e) {
    echo "Read-only properties cannot be reassigned";
}
```

2. **Type-Safe Initialization**

```php
$user = User::from([
    'name' => 123,       // Integer will be converted to string
    'age' => '35'        // String will be converted to integer
]);

echo $user->name;  // Output: "123"
echo $user->age;   // Output: 35
```

3. **Constructor Initialization**

```php
use Astral\Serialize\Serialize;

class User extends Serialize {
    public function __construct(
        public readonly string $name,
        public readonly int $age
    ) {
        // Can add additional validation or processing logic in the constructor
        if (strlen($name) < 2) {
            throw new \InvalidArgumentException('Name is too short');
        }
    }
}
```

## DTO Conversion

### Type Conversion

#### Basic Type Conversion

##### Method One: Constructor Property Promotion

```php
use Astral\Serialize\Serialize;

class Profile extends Serialize {
    public function __construct(
        public string $username,
        public int $score,
        public float $balance,
        public bool $isActive
    ) {}
}
```

##### Method Two: Traditional Property Definition

```php
use Astral\Serialize\Serialize;

class Profile extends Serialize {
    public string $username;
    public int $score;
    public float $balance;
    public bool $isActive;
}

// Both methods support the same type conversion
$profile = Profile::from([
    'username' => 123,        // Integer converted to string
    'score' => '100',         // String converted to integer
    'balance' => '99.99',     // String converted to float
    'isActive' => 1           // Number converted to boolean
]);

// Convert to array
$profileArray = $profile->toArray();
```

##### Method Three: Read-Only Properties

```php
use Astral\Serialize\Serialize;

class Profile extends Serialize {
    public readonly string $username;
    public readonly int $score;
    public readonly float $balance;
    public readonly bool $isActive;

    // Manual initialization
    public function __construct(
        string $username, 
        int $score, 
        float $balance, 
        bool $isActive
    ) {
        $this->username = $username;
        $this->score = $score;
        $this->balance = $balance;
        $this->isActive = $isActive;
    }
}
```

Regardless of the method used, the `Serialize` class will work normally and provide the same type conversion and serialization functionality.

#### Enum Conversion

Enum conversion provides a powerful and flexible enum handling mechanism, supporting multiple enum types and conversion scenarios.

- Supports enums with `tryFrom()` and `cases()` methods
- Automatically converts strings to enum instances during input
- Automatically converts enums to strings (enum names) during output
- Provides flexible and safe enum handling mechanisms

##### Regular Enum

```php
enum UserRole {
    case ADMIN;
    case EDITOR;
    case VIEWER;
}

class ComplexUser extends Serialize {

    public UserRole $role;

    // Supports multiple enum types
    public UserStatus|UserRole $mixedStatus;
}

$complexUser = ComplexUser::from([
    'role' => 'ADMIN',           // Automatically converted to UserRole::ADMIN
    'mixedStatus' => 'ACTIVE'    // Can be UserStatus or UserRole
]);

echo $complexUser->role; // Returns UserRole enum instance

$complexUserArray = $complexUser->toArray();
// $complexUserArray contents:
// [
//     'role' => 'ADMIN',
//     'mixedStatus' => 'ACTIVE'
// ]
```

##### Backed Enum

```php
use Astral\Serialize\Serialize;

// BackedEnum
enum UserStatus: string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
}

// Define a user class with enum
class User extends Serialize {
    public string $name;

    // Supports UnitEnum and BackedEnum
    public UserStatus $status;

    // Supports multiple enum types
    public UserStatus|string $alternateStatus;
}

// Create user object
$user = User::from([
    'name' => 'John Doe',
    'status' => 'active',           // Automatically converted to UserStatus::ACTIVE
    'alternateStatus' => 'inactive' // Supports string or enum values
]);

var_dump($user->status); // Output: UserStatus::ACTIVE

// Convert to array
$userArray = $user->toArray();
// $userArray contents:
// [
//     'name' => 'John Doe',
//     'status' => 'ACTIVE',        // Output enum name
//     'alternateStatus' => 'INACTIVE'
// ]
```

#### Null Value Conversion Rules Detailed Example

When the property is not a nullable type (`?type`), `null` values will be automatically converted based on the target type:

```php
use Astral\Serialize\Serialize;

class NullConversionProfile extends Serialize {
    public string $username;
    public int $score;
    public float $balance;
    public array $tags;
    public object $metadata;
}

// Null value conversion example
$profile = NullConversionProfile::from([
    'username' => null,   // Converted to empty string ''
    'score' => null,      // Converted to 0
    'balance' => null,    // Converted to 0.0
    'tags' => null,       // Converted to empty array []
    'metadata' => null    // Converted to empty object new stdClass()
]);

// Verify conversion results
echo $profile->username;   // Output: ""（empty string）
echo $profile->score;      // Output: 0
echo $profile->balance;    // Output: 0.0
var_dump($profile->tags);  // Output: array(0) {}
var_dump($profile->metadata);  // Output: object(stdClass)#123 (0) {}

// Special handling for boolean values
try {
    NullConversionProfile::from([
        'isActive' => null  // This will throw a type error
    ]);
} catch (\TypeError $e) {
    echo "Boolean type does not support null values: " . $e->getMessage();
}
```

#### Nullable Type Solution

For scenarios requiring `null` acceptance, use nullable types:

```php
use Astral\Serialize\Serialize;

class FlexibleProfile extends Serialize {
    public function __construct(
        public ?string $username,
        public ?int $score,
        public ?object $metadata,
        public ?array $tags
    ) {}
}

// Create an object with null values
$profile = FlexibleProfile::from([
    'username' => null,           // Allows null
    'score' => null,              // Allows null
    'metadata' => null,           // Allows null
    'tags' => null                // Allows null
]);

// Convert to array
$profileArray = $profile->toArray();
// $profileArray contents:
// [
//     'username' => null,
//     'score' => null,
//     'metadata' => null,
//     'tags' => null
// ]

// Validate nullable type behavior
echo $profile->username;         // Output null
```

#### Union Types

1. Can mix basic and object types
2. Object Hierarchy Matching
    - For multiple object types, the most matching type will be selected
    - Supports intelligent matching of inheritance hierarchy
3. Dynamic Type Handling
    - Automatically handles input of different types
    - Provides more flexible data modeling

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
    // Supports integer or string type identifiers
    public int|string $flexibleId;

    // Supports user object or integer identifier
    public User|int $userIdentifier;

    // Supports complex union types
    public AdminUser|User|int $complexIdentifier;
}

// Scenario 1: Use integer as flexibleId
$data1 = FlexibleData::from(
    flexibleId : 123,
    userIdentifier : 456,
    complexIdentifier : 789
);

$data1Array = $data1->toArray();
// $data1Array contents:
// [
//     'flexibleId' => 123,
//     'userIdentifier' => 456,
//     'complexIdentifier' => 789
// ]

// Scenario 2: Use string as flexibleId
$data2 = FlexibleData::from(
    flexibleId : 'ABC123',
    userIdentifier : [
        'name' => 'John',
        'age' => 30
    ],
    complexIdentifier : [
        'name' => 'Jane',
        'age' => 25
    ]
);

echo $data2->userIdentifier; // Output User object
echo $data2->complexIdentifier; // Output User object

$data2Array = $data2->toArray();
// $data2Array contents:
// [
//     'flexibleId' => 'ABC123',
//     'userIdentifier' => User Object (
//         ['name' => 'John', 'age' => 30]
//     ),
//     'complexIdentifier' => User Object (
//         ['name' => 'Jane', 'age' => 25]
//     )
// ]

// Scenario 3: Use admin user
$data3 = FlexibleData::from(
    flexibleId : 'USER001',
    userIdentifier : [
        'name' => 'Bob',
        'age' => 35,
        'role' => 'admin'
    ],
    complexIdentifier : [
        'name' => 'Alice',
        'age' => 40,
        'role' => 'super_admin'
    ]
);

echo $data2->userIdentifier; // Output User object
echo $data2->complexIdentifier; // Output AdminUser object

$data3Array = $data3->toArray();
// $data3Array contents:
// [
//     'flexibleId' => 'USER001',
//     'userIdentifier' => User Object (
//         ['name' => 'Bob', 'age' => 35]
//     ),
//     'complexIdentifier' => AdminUser Object (
//         ['name' => 'Alice', 'age' => 40, 'role' => 'super_admin']
//     )
// ]
```

#### Array Object Conversion

##### phpDoc Definition

```php
use Astral\Serialize\Serialize;

// Define basic array types
class ArrayOne extends Serialize {
    public string $type = 'one';
    public string $name;
}

class ArrayTwo extends Serialize {
    public string $type = 'two';
    public string $code;
}

class MultiArraySerialize extends Serialize {
    // Scenario 1: Mixed type array
    /** @var (ArrayOne|ArrayTwo)[] */
    public array $mixedTypeArray;

    // Scenario 2: Multiple type arrays
    /** @var ArrayOne[]|ArrayTwo[] */
    public array $multiTypeArray;

    // Scenario 3: Key-value mixed type
    /** @var array(string, ArrayOne|ArrayTwo) */
    public array $keyValueMixedArray;
}

// Scenario 1: Mixed type array
$data1 = MultiArraySerialize::from(
    mixedTypeArray : [
        ['name' => 'John'],           //  Convert to ArrayOne object
        ['code' => 'ABC123'],         // Convert to ArrayTwo object
        ['name' => 'Jane'],            // Convert to ArrayOne object
        ['code' => 'DEF456']          // Convert to ArrayTwo object
    ]
);

$data1Array = $data1->toArray();
// $data1Array contents:
// [
//     'mixedTypeArray' => [
//           [0] => ArrayOne Object
//                (
//                    ['name' => 'John', 'type' => 'one'],
//                )
//           [1] => ArrayTwo Object
//                (
//                    ['code' => 'ABC123', 'type' => 'two'],
//                )
//           [2] => ArrayOne Object
//                (
//                    ['name' => 'Jane', 'type' => 'one'],
//                )
//           [3] => ArrayTwo Object
//                (
//                    ['code' => 'DEF456', 'type' => 'two'],
//                )
//     ]
// ]

// Scenario 2: Multiple type arrays
$data2 = MultiArraySerialize::from(
    multiTypeArray:[
        ['name' => 'Bob'],            // Convert to ArrayOne object
        ['name' => 'Alice'],            // Convert to ArrayOne object
        ['code' => 'GHI789']          // Convert to ArrayTwo object
    ]
);

$data2Array = $data2->toArray();
// $data2Array contents:
// [
//     'multiTypeArray' => [
//         ArrayOne Object (
//             ['name' => 'Bob', 'type' => 'one']
//         ),
//         ArrayOne Object (
//             ['name' => 'Alice', 'type' => 'one']
//         ),
//         ArrayTwo Object (
//             ['code' => 'GHI789', 'type' => 'two']
//         )
//     ]
// ]

// Scenario 3: Key-value mixed type
$data3 = MultiArraySerialize::from(
    keyValueMixedArray: [
        'user1' => ['name' => 'John'],           // Convert to ArrayOne object
        'system1' => ['code' => 'ABC123'],       // Convert to ArrayTwo object
        'user2' => ['name' => 'Jane']            // Convert to ArrayOne object
    ]
);

$data3Array = $data3->toArray();
// $data3Array contents:
// [
//     'keyValueMixedArray' => [
//         'user1' => ArrayOne Object (
//             ['name' => 'John', 'type' => 'one']
//         ),
//         'system1' => ArrayTwo Object (
//             ['code' => 'ABC123', 'type' => 'two']
//         ),
//         'user2' => ArrayOne Object (
//             ['name' => 'Jane', 'type' => 'one']
//         )
//     ]
// ]

// Scenario 4: Handling unmatched cases
$data4 = MultiArraySerialize::from(
    mixedTypeArray : [
        ['unknown' => 'data1'],
        ['another' => 'data2']
    ]
);

$data4Array = $data4->toArray();
// $data4Array contents:
// [
//     'mixedTypeArray' => [
//         ['unknown' => 'data1'],
//         ['another' => 'data2']
//     ]
// ]
```

### Annotation Class Usage

#### Property Grouping

Property grouping provides a flexible way to control the input and output behavior of properties, allowing fine-grained data conversion management in different scenarios.

##### Basic Usage

Use the `#[Groups]` annotation on properties to specify the groups they belong to.

```php
use Astral\Serialize\Attributes\Groups;
use Astral\Serialize\Serialize;

class User extends Serialize {

    #[Groups('update','detail')]
    public string $id;

    #[Groups('create', 'update', 'detail')]
    public string $name;

    #[Groups('create','detail')]
    public string $username;

    #[Groups('other')]
    public string $sensitiveData;

    // Properties without a specified group will be in the default group
    public string $noGroupInfo;

    // Constructor parameters also support grouping
    public function __construct(
        #[Groups('create','detail')]
        public readonly string $email,
        
        #[Groups('update','detail')]
        public readonly int $score
    ) {}
}

// Use default group to display all information
$user1 = User::from(
    id:1,
    name: 'Jane',
    score: 100,
    username: 'username',
    email: 'jane@example.com',
    sensitiveData:'Confidential info',
    noGroupInfo:'Default group info'
);

// Use default group toArray, display all information
$defaultArray = $user1->toArray();
// $defaultArray contents:
// [
//     'id' => '1',
//     'name' => 'Jane',
//     'username' => 'username',
//     'score' => 100,
//     'email' => 'jane@example.com',
//     'sensitiveData' => 'Confidential info',
//     'noGroupInfo' => 'Default group info'
// ]

// Use create group to create user, only accept data with create group
$user2 = User::setGroups(['create'])->from(
    id:1,
    name: 'Jane',
    score: 100,
    username: 'username',
    email: 'jane@example.com',
    sensitiveData:'Confidential info',
    noGroupInfo:'Default group info'
);

// Use create group toArray
$createArray = $user2->toArray();
// $createArray contents:
// [
//     'name' => 'Jane',
//     'username' => 'username',
//     'email' => 'jane@example.com',
// ]

// Use update group to update user, only accept data with update group
$user3 = User::setGroups(['update'])->from(
    id:1,
    name: 'Jane',
    score: 100,
    username: 'username',
    email: 'jane@example.com',
    sensitiveData:'Confidential info',
    noGroupInfo:'Default group info'
);

// Use update group toArray
$updateArray = $user3->toArray();
// $updateArray contents:
// [
//     'id' => '1',
//     'name' => 'Jane',
//     'score' => 100,
// ]

// Use detail and other groups to display user, accept data with detail and other groups
$user4 = User::setGroups(['detail','other'])->from(
    id:1,
    name: 'Jane',
    score: 100,
    username: 'username',
    email: 'jane@example.com',
    sensitiveData:'Confidential info',
    noGroupInfo:'Default group info'
);

// Use multiple groups toArray
$multiGroupArray = $user4->toArray();
// $multiGroupArray contents:
// [
//     'id' => '1',
//     'name' => 'Jane',
//     'username' => 'username',
//     'score' => 100,
//     'email' => 'jane@example.com',
//     'sensitiveData' => 'Confidential info',
// ]
```

##### Nested Class Group Display

```php
class ComplexUser extends Serialize {
    
    public string $name;
    
    public int $sex;

    public ComplexNestedInfo $info;
}

class ComplexNestedInfo extends Serialize {
    
    #[Groups(ComplexAUser::class)]
    public float $money;

    public string $currency;
}

// ComplexNestedInfo will hide currency
$adminUser = ComplexUser::from(
    name: 'John',
    sex: 1,
    info: [
        'money' => 100.00,
        'currency' => 'USD'
    ]
);

// Output data
$adminUserArray = $adminUser->toArray();
// $adminUserArray contents:
// [
//     'name' => 'John',
//     'sex' => 1,
//     'info' => ComplexNestedInfo Object ([
//         'money' => 100.00
//     ])
// ]
```

#### Name Mapping

##### Basic Usage

```php
use Astral\Serialize\Attributes\InputName;
use Astral\Serialize\Attributes\OutputName;
use Astral\Serialize\Serialize;

class User extends Serialize {
    // Use different property name for input
    #[InputName('user_name')]
    public string $name;

    // Use different property name for output
    #[OutputName('user_id')]
    public int $id;

    // Support different input and output names
    #[InputName('register_time')]
    #[OutputName('registeredAt')]
    public DateTime $createdAt;
}

// Use input data with different names
$user = User::from([
    'user_name' => 'John',       // Mapped to $name
    'id' => 123,                // Remains unchanged
    'register_time' => '2023-01-01 10:00:00'  // Mapped to $createdAt
]);

// Output data
$userArray = $user->toArray();
// $userArray contents:
// [
//     'name' => 'John',
//     'user_id' => 123,
//     'registeredAt' => '2023-01-01 10:00:00'
// ]
```

##### Multiple Input/Output Name Handling

```php
use Astral\Serialize\Attributes\InputName;
use Astral\Serialize\Attributes\OutputName;
use Astral\Serialize\Serialize;

class MultiOutputUser extends Serialize {
    // Multiple output names
    #[OutputName('user_id')]
    #[OutputName('id')]
    #[OutputName('userId')]
    public int $id;

    // Multiple input names, first matching name will be used
    #[InputName('user_name')]
    #[InputName('other_name')]
    #[InputName('userName')]
    public int $name;
}

// Scenario 1: Use first matching input name
$user1 = MultiOutputUser::from([
    'user_name' => 'John'  // Use 'user_name'
]);
echo $user1->name;  // Output 'John'

// Scenario 2: Use second matching input name
$user2 = MultiOutputUser::from([
    'other_name' => 'Jane'  // Use 'other_name'
]);
echo $user2->name;  // Output 'Jane'

// Scenario 3: Use last input name
$user3 = MultiOutputUser::from([
    'userName' => 'Bob'  // Use 'userName'
]);
echo $user3->name;  // Output 'Bob'

// Scenario 4: Multiple inputs, first matching name used
$user4 = MultiOutputUser::from([
    'userName' => 'Bob',
    'other_name' => 'Jane',
    'user_name' => 'John',
]);
echo $user4->name;  // Output 'John'

// Create user object
$user = MultiOutputUser::from([
    'id' => 123,
    'name' => 'John'
]);

// Convert to array
$userArray = $user->toArray();
// $userArray contents:
// [
//     'user_id' => 123,
//     'id' => 123,
//     'userId' => 123,
// ]
```

#### Complex Mapping Scenarios

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

// Handle complex input structures
$complexUser = ComplexUser::from([
    'user_profile' => [
        'nickname' => 'John',
        'age' => 25
    ],
    'user_tags' => ['developer', 'programmer']
]);

// Convert to standard array
$complexUserArray = $complexUser->toArray();
// $complexUserArray contents:
// [
//     'profile' => UserProfile Object ([
//         'nickname' => 'John',
//         'age' => 25
//     ]),
//     'tags' => ['developer', 'programmer']
// ]
```

##### Mapper Mapping

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

class User extends Serialize {
    // Directly specify mapping names
    #[InputName('user_name')]
    #[OutputName('userName')]
    public string $name;

    // Use mapper for style conversion
    #[InputName(CamelCaseMapper::class)]
    #[OutputName(SnakeCaseMapper::class)]
    public int $userId;

    // Support multiple mappings and groups
    #[InputName('email', groups: 'profile')]
    #[OutputName('userEmail', groups: 'api')]
    public string $email;
}

// Use different mapping strategies
$user = User::from([
    'user_name' => 'John',       // Mapped to $name
    'user_id' => 123,            // Use CamelCaseMapper conversion
    'email' => 'user@example.com' // Only effective in 'profile' group
]);

// Output with different mappings
$userArray = $user->toArray(
    inputGroups: ['profile'],   // Use only input mappings in 'profile' group
    outputGroups: ['api']       // Use only output mappings in 'api' group
);
```
  
##### Global Class Mapping

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
    // Class-level mapping automatically applies to all properties
    public string $firstName;
    public string $lastName;
    public int $userId;
    public DateTime $registeredAt;
}

// Use global mapping
$user = GlobalMappedUser::from([
    'first_name' => 'John',        // From snake_case to firstName
    'last_name' => 'Doe',          // From snake_case to lastName
    'user_id' => 123,              // From snake_case to userId
    'registered_at' => '2023-01-01' // From snake_case to registeredAt
]);

// Output will be converted to camelCase
$userArray = $user->toArray();
// $userArray contents:
// [
//     'firstName' => 'John',
//     'lastName' => 'Doe',
//     'userId' => 123,
//     'registeredAt' => '2023-01-01'
// ]
```

###### Property Mapping Takes Precedence Over Class-Level Mapping

```php
#[InputName(SnakeCaseMapper::class)]
class PartialOverrideUser extends Serialize {
    #[InputName(PascalCaseMapper::class)]
    public string $userName;  // Prioritize PascalCase mapping
    
    public string $userEmail;  // Continue using class-level global mapping
}

$partialUser = PartialOverrideUser::from([
    'User_name' => 'John',     // Use snake_case mapping
    'UserName' => 'Jane',      // Use PascalCase mapping
    'user_email' => 'user@example.com' // Use snake_case mapping
]);

$partialUser->toArray();
// $partialUser contents:
// [
//     'userName' => 'Jane',
//     'userEmail' => 'user@example.com',
// ]
```

###### Global Class Mapping with Groups

Needs to be used in conjunction with `Groups` annotation

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

// Use admin group
$complexUser = ComplexMappedUser::setGroup('external')->from(
    first_name: 'John',    
    last_name: 'Doe',
    full_name: 'John Doe'
);

$complexUser = $complexUser->toArray();
// $complexUser contents:
// [
//    'FirstName' => 'John',
//    'LastName' => 'Doe',
//    'FullName' => 'John Doe',
// ]

// If specific OutputName/InputName is familiar, attribute rules take priority
// Use public group
$complexUser = ComplexMappedUser::setGroup('api')->from(
    first_name: 'John',    
    last_name: 'Doe',
    full_name: 'John Doe'
);

$complexUser = $complexUser->toArray();
// $complexUser contents:
// [
//     'firstName' => 'John',
//     'lastName' => 'Doe',
//     'userEmail' => 'John Doe',
// ]
```

#### Custom Mapper

```php
// Custom mapper needs to extend NameMapper and implement resolve
class CustomMapper implements NameMapper {
    public function resolve(string $name): string {
        // Implement custom naming conversion logic
        return str_replace('user', 'customer', $name);
    }
}

class AdvancedUser extends Serialize {
    #[InputName(CustomMapper::class)]
    public string $name;
}
```

#### Field Ignoring

1. **Security Control**
   - Prevent accidental leakage of sensitive information
   - Fine-grained control of data input and output

2. **Data Filtering**
   - Filter fields based on different scenarios
   - Customize data views for different APIs or user roles

3. **Performance Optimization**
   - Reduce serialization overhead for unnecessary fields
   - Streamline data transmission

##### Basic Usage

```php
use Astral\Serialize\Attributes\InputIgnore;
use Astral\Serialize\Attributes\OutputIgnore;
use Astral\Serialize\Serialize;

class User extends Serialize {

    public string $name;

    // Fields ignored during input
    #[InputIgnore]
    public string $internalId;

    // Fields ignored during output
    #[OutputIgnore]
    public string $tempData;
}

// Create user object
$user = User::from([
    'name' => 'John',
    'internalId' => 'secret123',  // This field will be ignored
    'tempData' => 'temporary'     // This field will be ignored
]);

echo $user->internalId; // This will output ''

// Convert to array
$userArray = $user->toArray();
// $userArray contents:
// [
//     'name' => 'John',
//     'internalId' => '',
// ]
```

##### Group Ignoring

Group ignoring needs to be used in conjunction with the Groups annotation

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

// Default group
$complexUser = ComplexUser::from([
    'name' => 'John',
    'secretKey' => 'confidential',
    'sensitiveInfo' => 'Sensitive information',
    'globalInputIgnore' => 'Global input ignore',
    'globalOutputIgnore' => 'Global output ignore'
]);

echo $complexUser->globalInputIgnore; // Output ''
echo $complexUser->globalOutputIgnore; // Output 'Global output ignore'

$complexUser = $complexUser->toArray();
// $complexUser contents:
// [
//    'name' => 'John',
//    'secretKey' => 'confidential',
//    'sensitiveInfo' => 'Sensitive information',
//    'globalInputIgnore' => '',
// ]

// Use admin group
$complexUser = ComplexUser::setGroups('admin')->from([
    'name' => 'John',
    'secretKey' => 'confidential',
    'sensitiveInfo' => 'Sensitive information',
    'globalInputIgnore' => 'Global input ignore',
    'globalOutputIgnore' => 'Global output ignore'
]);

$complexUser = $complexUser->toArray();
// $complexUser contents:
// [
//     'name' => '',
//     'secretKey' => 'confidential',
//     'globalInputIgnore' => '',
// ]

// Use public group
$complexUser = ComplexUser::setGroups('public')->from([
    'name' => 'John',
    'secretKey' => 'confidential',
    'sensitiveInfo' => 'Sensitive information',
    'globalInputIgnore' => 'Global input ignore',
    'globalOutputIgnore' => 'Global output ignore'
]);

$complexUser = $complexUser->toArray();
// $complexUser contents:
// [
//     'name' => 'John',
//     'globalInputIgnore' => '',
// ]
```

### Advanced Faker Usage

#### Custom Faker Providers

```php
use Astral\Serialize\Attributes\FakerValue;
use Astral\Serialize\Serialize;
use Faker\Provider\Base as FakerProvider;

// Create a custom Faker provider
class CustomProvider extends FakerProvider {
    public static function customField($input = null) {
        // Implement custom generation logic
        return $input ? "Custom: $input" : "Default Custom Value";
    }
}

class CustomFakerUser extends Serialize {
    // Use custom provider
    #[FakerValue('customField', provider: CustomProvider::class)]
    public string $customField;

    // Use custom provider with input
    #[FakerValue('customField:specialInput', provider: CustomProvider::class)]
    public string $specialCustomField;
}

$customUser = CustomFakerUser::faker();
$customUserArray = $customUser->toArray();
// $customUserArray contents:
// [
//     'customField' => 'Default Custom Value',
//     'specialCustomField' => 'Custom: specialInput'
// ]
```

#### Faker with Constraints

```php
class ConstrainedFakerUser extends Serialize {
    // Generate age between 18 and 65
    #[FakerValue('numberBetween:18,65')]
    public int $age;

    // Generate email with specific domain
    #[FakerValue('email:gmail.com')]
    public string $email;

    // Generate unique values
    #[FakerValue('unique:uuid')]
    public string $userId;
}

$constrainedUser = ConstrainedFakerUser::faker();
$constrainedUserArray = $constrainedUser->toArray();
// Guaranteed to have:
// - Age between 18 and 65
// - Email from gmail.com
// - Unique UUID
```

#### Nested Object Faker

```php
class Address extends Serialize {
    #[FakerValue('streetAddress')]
    public string $street;

    #[FakerValue('city')]
    public string $city;

    #[FakerValue('country')]
    public string $country;
}

class ComplexFakerUser extends Serialize {
    #[FakerValue('name')]
    public string $name;

    #[FakerCollection(Address::class, num: 2)]
    public array $addresses;
}

$complexUser = ComplexFakerUser::faker();
$complexUserArray = $complexUser->toArray();
// Will generate a user with name and two randomly generated addresses
```

### Faker Localization

```php
class LocalizedFakerUser extends Serialize {
    // Use Japanese faker for name generation
    #[FakerValue('name', locale: 'ja_JP')]
    public string $japaneseName;

    // Use French faker for phone number
    #[FakerValue('phoneNumber', locale: 'fr_FR')]
    public string $frenchPhoneNumber;
}

$localizedUser = LocalizedFakerUser::faker();
$localizedUserArray = $localizedUser->toArray();
// Will generate names and phone numbers specific to Japanese and French locales
```

### Performance and Configuration

```php
class PerformanceFakerUser extends Serialize {
    // Seed for reproducible results
    #[FakerValue('name', seed: 12345)]
    public string $consistentName;

    // Limit generation attempts
    #[FakerValue('unique:email', maxAttempts: 10)]
    public string $uniqueEmail;
}

// Configure global Faker settings
Serialize::setFakerConfig([
    'locale' => 'en_US',
    'seed' => 42,
    'unique_max_retries' => 50
]);
```

### Error Handling and Validation

```php
class StrictFakerUser extends Serialize {
    // Throw exception if unique generation fails
    #[FakerValue('unique:uuid', maxAttempts: 5, throwOnFailure: true)]
    public string $strictUniqueId;
}

try {
    $strictUser = StrictFakerUser::faker();
} catch (FakerGenerationException $e) {
    // Handle unique generation failure
    echo "Failed to generate unique value: " . $e->getMessage();
}
```

### Nested Object Mocking

#### Basic Usage

```php
class ComplexUserFaker extends Serialize {
    #[FakerObject(UserProfile::class)]
    public UserProfile $profile;
}
```

#### Demonstration Example

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
// $complexUserFakerArray contents:
// [
//     'profile' => UserProfile Object (
//         ['nickname' => 'RandomNickname', 'age' => 28, 'email' => 'random.user@example.com', 'avatar' => 'https://example.com/avatars/random-avatar.jpg']
//     ),
//     'primaryTag' => UserTag Object (
//         ['name' => 'Developer', 'color' => '#007bff']
//     )
// ]
```

### Faker Class Method Mocking

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

#### Complete Example

```php
use Astral\Serialize\Serialize;
use Astral\Serialize\Attributes\Faker\FakerMethod;
use Astral\Serialize\Attributes\Faker\FakerObject;
use Astral\Serialize\Attributes\Faker\FakerCollection;

// User Profile Class
class UserProfile extends Serialize {
    public string $nickname;
    public int $age;
    public string $email;
    public array $types = ['type1' => 'money', 'type2' => 'score'];
}

// User Service Class, providing data generation methods
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

// Faker Method Mocking Example
class UserFaker extends Serialize {
    // Use method to generate simple data
    #[FakerMethod(UserService::class, 'generateUserData')]
    public array $userData;

    // Use method to generate object
    #[FakerMethod(UserService::class, 'generateUserProfile')]
    public UserProfile $userProfile;

    // Get specific attribute
    #[FakerMethod(UserService::class, 'generateUserProfile', returnType: 'age')]
    public int $age;

    // Get specific attribute with multi-level access using [.]
    #[FakerMethod(UserService::class, 'generateUserProfile', returnType: 'types.type2')]
    public string $type2;

    // Pass parameters
    #[FakerMethod(UserService::class, 'generateUserList', params: ['count' => 3])]
    public array $userList;
}

// Generate mock data
$userFaker = UserFaker::faker();

// Convert to array
$userFakerArray = $userFaker->toArray();
// $userFakerArray contents:
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
//     'age' => 99 , // Randomly generated
//     'type2' => 'score',
//     'userList' => [
//         ['name' => 'User 0', 'email' => 'user0@example.com'],
//         ['name' => 'User 1', 'email' => 'user1@example.com'],
//         ['name' => 'User 2', 'email' => 'user2@example.com']
//     ]
// ]
```