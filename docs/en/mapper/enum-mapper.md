## Enum Mapping

Enum mapping provides a powerful and flexible mechanism for handling enums, supporting multiple enum types and conversion scenarios.

- Supports enum types with `tryFrom()` and `cases()` methods
- Automatically converts strings to enum instances on input
- Automatically converts enums to strings (enum name) on output
- Provides a flexible and safe mechanism for enum handling

### Regular Enum

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
    'role' => 'ADMIN',           // Automatically converts to UserRole::ADMIN
    'mixedStatus' => 'ACTIVE'    // Can be UserStatus or UserRole
]);

echo $complexUser->role; // Returns UserRole enum instance

$complexUserArray = $complexUser->toArray();
// Content of $complexUserArray:
// [
//     'role' => 'ADMIN',
//     'mixedStatus' => 'ACTIVE'
// ]
```

### Backed Enum

```php
use Astral\Serialize\Serialize;

// BackedEnum
enum UserStatus: string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
}

// Define a user class with enums
class User extends Serialize {
    public string $name;

    // Supports UnitEnum and BackedEnum
    public UserStatus $status;

    // Supports multiple enum types
    public UserStatus|string $alternateStatus;
}

// Create user object
$user = User::from([
    'name' => 'Job',
    'status' => 'active',           // Automatically converts to UserStatus::ACTIVE
    'alternateStatus' => 'inactive' // Supports string or enum value
]);

var_dump($user->status); // Output: UserStatus::ACTIVE

// Convert to array
$userArray = $user->toArray();
// Content of $userArray:
// [
//     'name' => 'Job',
//     'status' => 'ACTIVE',        // Output enum name
//     'alternateStatus' => 'INACTIVE'
// ]
```