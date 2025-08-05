## Mapper Mapping

### Property Mapping

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
    // Directly specify the mapping name
    #[InputName('user_name', groups: ['profile','api'])]
    #[OutputName('userName', groups: ['profile','api'])]
    public string $name;

    // Use a mapper for style conversion
    #[InputName(CamelCaseMapper::class, groups: ['profile','api'])]
    #[OutputName(SnakeCaseMapper::class, groups: ['profile','api'])]
    public int $userId;

    // Supports multiple mappings and groups
    #[InputName('profile-email', groups: 'profile')]
    #[OutputName('userEmail', groups: 'profile')]
    public string $email;
}

// Use different mapping strategies
$user = User::setGroups('profile')::from([
    'user_name' => 'Job',       // Mapped to $name
    'userId' => 123,           // Converted by CamelCaseMapper
    'profile-email' => 'user@example.com' // Only effective in 'profile' group
]);

// Apply different mappings on output
$userArray = $user->toArray();
// $userArray toArray:
// [
//     'userName' => 'Job',
//     'user_id' => '123',
//     'userEmail' => user@example.com,
// ]
```

### Global Class Mapping

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
    // Class-level mapping is automatically applied to all properties
    public string $firstName;
    public string $lastName;
    public int $userId;
    public DateTime $registeredAt;
}

// Use global mapping
$user = GlobalMappedUser::from([
    'first_name' => 'Job',        // Mapped from snake_case to firstName
    'last_name' => 'Doe',         // Mapped from snake_case to lastName
    'user_id' => 123,            // Mapped from snake_case to userId
    'registered_at' => '2023-01-01' // Mapped from snake_case to registeredAt
]);

// Output will be converted to camelCase
$userArray = $user->toArray();
// $userArray toArray:
// [
//     'firstName' => 'Job',
//     'lastName' => 'Doe',
//     'userId' => 123,
//     'registeredAt' => '2023-01-01'
// ]
```

### Grouped Usage of Global Class Mapping

Requires use with the `Groups` annotation

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

// Using 'external' group
$complexUser = ComplexMappedUser::setGroup('external')->from(
    first_name :'Job',    
    last_name :'Doe'
    full_name: 'Job Don'
);

$complexUser = $complexUser->toArray();
// $complexUser toArray:
// [
//     'FirstName' => 'Job',
//     'LastName' => 'Don',
//     'FullName' => Job Don,
// ]

// If InputName/OutputName is specified, property rules take precedence
// Using 'api' group
$complexUser = ComplexMappedUser::setGroup('api')->from(
    first_name :'Job',    
    last_name :'Don'
    full_name: 'Job Don'
);

$complexUser = $complexUser->toArray();
// $complexUser toArray:
// [
//     'FirstName' => 'Job',
//     'LastName' => 'Don',
//     'userEmail' => Job Don,
// ]
```
### Custom Mapper

```php
// Custom mappers need to inherit NameMapper and implement resolveclass CustomMapper implements NameMapper {
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

### Tips: Property mapping takes precedence over class-level mapping

```php

#[InputName(SnakeCaseMapper::class)]
class PartialOverrideUser extends Serialize {
    #[InputName(PascalCaseMapper::class)]
    public string $userName;   // Uses PascalCase mapping preferentially
    
    public string $userEmail;  // Continues to use class-level global mapping
}

$partialUser = PartialOverrideUser::from([
    'User_name' => 'Tom',      // Uses SnakeCase mapping
    'UserName' => 'Job Don',      // Uses PascalCase mapping
    'user_email' => 'user@example.com' // Uses SnakeCase mapping
]);

$partialUser->toArray();
// Content of $partialUser:
// [
//     'userName' => 'Tom',
//     'userEmail' => 'user@example.com',
// ]
```