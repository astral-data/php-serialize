## Field Ignoring

1. **Security Control**
    - Prevent accidental leakage of sensitive information
    - Fine-grained control of data input and output

2. **Data Filtering**
    - Filter fields according to different scenarios
    - Customize data views for different APIs or user roles

3. **Performance Optimization**
    - Reduce serialization overhead of unnecessary fields
    - Streamline data transmission

### Basic Usage

```php
use Astral\Serialize\Attributes\InputIgnore;
use Astral\Serialize\Attributes\OutputIgnore;
use Astral\Serialize\Serialize;


class User extends Serialize {

    public string $name;

    // Field ignored during input
    #[InputIgnore]
    public string $internalId;

    // Field ignored during output
    #[OutputIgnore]
    public string $tempData;
}

// Create user object
$user = User::from([
    'name' => 'Job',
    'internalId' => 'secret123',  // This field will be ignored
    'tempData' => 'temporary'     // This field will be ignored
]);

echo  $user->internalId; // This will output ''

// Convert to array
$userArray = $user->toArray();
// Content of $userArray:
// [
//     'name' => 'John Doe',
//     'internalId' => '',
// ]
```

### Ignore by Group

Ignoring by group requires using the Groups annotation together

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
    'name' => '张三',
    'secretKey' => 'confidential',
    'sensitiveInfo' => 'Confidential info',
    'globalInputIgnore' => '全局输入忽略',
    'globalOutputIgnore' => '全局输出忽略'
]);

echo $complexUser->globalInputIgnore; // Output ''
echo $complexUser->globalOutputIgnore; // Output 'Global output ignore'

$complexUser = $complexUser->toArray();
// $complexUser toArray:
// [
//    'name' => 'Job',
//    'secretKey' => 'confidential',
//    'sensitiveInfo' => 'Confidential info',
//    'globalInputIgnore' => '',
// ]


// Use admin group
$complexUser = ComplexUser::setGroups('admin')->from([
    'name' => 'Job Doe',
    'secretKey' => 'confidential',
    'sensitiveInfo' => 'Confidential info',
    'globalInputIgnore' => 'Global input ignore',
    'globalOutputIgnore' => 'Global output ignore'
]);

$complexUser = $complexUser->toArray();
// $complexUser toArray:
// [
//     'name' => '',
//     'secretKey' => 'confidential',
// 'globalInputIgnore' => '',
// ]

// Use public group
$complexUser = ComplexUser::setGroups('public')->from([
    'name' => 'Job Doe',
    'secretKey' => 'confidential',
    'sensitiveInfo' => 'Confidential info',
    'globalInputIgnore' => 'Global input ignore',
    'globalOutputIgnore' => 'Global output ignore'
]);

$complexUser = $complexUser->toArray();
// Content of $complexUser:
// [
//     'name' => 'Job Doe',
///    'globalInputIgnore' => '',
// ]
```