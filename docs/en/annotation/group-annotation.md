## Property Grouping (Groups)

Property grouping provides a flexible way to control the input and output behavior of properties, allowing fine-grained management of data conversion in different scenarios.

---

### Explanation of Grouping Principle

- Use the `#[Groups(...)]` annotation to assign a property to one or more groups.
- Supports:
    - **For input:** Filter data fields by group
    - **For output:** Select output fields by group
- Properties without a specified group will automatically be assigned to the `"default"` group.

---

### Basic Example

```php
use Astral\Serialize\Attributes\Groups;
use Astral\Serialize\Serialize;

class User extends Serialize {

    #[Groups('update', 'detail')]
    public string $id;

    #[Groups('create', 'update', 'detail')]
    public string $name;

    #[Groups('create', 'detail')]
    public string $username;

    #[Groups('other')]
    public string $sensitiveData;

    // Not assigned to a group, defaults to the 'default' group
    public string $noGroupInfo;

    public function __construct(
        #[Groups('create', 'detail')]
        public readonly string $email,

        #[Groups('update', 'detail')]
        public readonly int $score
    ) {}
}
```

### Receive by Group

```php
// Create a user with the 'create' group, only accepts fields in group=create
$user = User::setGroups(['create'])->from([
    'id' => 1,
    'name' => 'Job',
    'score' => 100,
    'username' => 'username',
    'email' => 'Job@example.com',
    'sensitiveData' => 'sensitive',
    'noGroupInfo' => 'noGroup'
]);

$user->toArray();
/*
[
    'name' => 'Job',
    'username' => 'username',
    'email' => 'Job@example.com',
]
*/
```

### Output by Group

```php
$user = User::from([
    'id' => 1,
    'name' => 'Job',
    'score' => 100,
    'username' => 'username',
    'email' => 'Job@example.com',
    'sensitiveData' => 'sensitive',
    'noGroupInfo' => 'noGroup'
]);

// 默认输出所有字段
$user->toArray();
/*
[
    'id' => '1',
    'name' => 'Job',
    'username' => 'username',
    'score' => 100,
    'email' => 'Job@example.com',
    'sensitiveData' => 'sensitive',
    'noGroupInfo' => 'noGroup'
]
*/

// 指定输出分组
$user->withGroups('create')->toArray();
/*
[
    'name' => 'Job',
    'username' => 'username',
    'email' => 'Job@example.com',
]
*/

$user->withGroups(['detail', 'other'])->toArray();
/*
[
    'id' => '1',
    'name' => 'Job',
    'username' => 'username',
    'score' => 100,
    'email' => 'Job@example.com',
    'sensitiveData' => 'sensitive',
]
*/
```

### Grouping of Nested Objects

```php
class ComplexUser extends Serialize {
    public string $name;
    public int $sex;
    public ComplexNestedInfo $info;
}

class ComplexNestedInfo extends Serialize {
    #[Groups(ComplexUser::class)]
    public float $money;

    public string $currency;
}

$adminUser = ComplexUser::from([
    'name' => 'Job',
    'sex' => 1,
    'info' => [
        'money' => 100.00,
        'currency' => 'CNY'
    ],
]);

// Only $money will be output in info
// Because ComplexNestedInfo is bound to the class Group of ComplexUser
$adminUser->toArray();
/*
[
    'name' => 'Job',
    'sex' => 1,
    'info' => [
        'money' => 100.00
    ]
]
*/
```