## Simple Property Faker

```php
class UserFaker extends Serialize {
    #[FakerValue('name')]
    public string $name;

    #[FakerValue('email')]
    public string $email;

    #[FakerValue('uuid')]
    public string $userId;

    #[FakerValue('phoneNumber')]
    public string $phone;

    #[FakerValue('age')]
    public int $age;

    #[FakerValue('boolean')]
    public bool $isActive;
}

$user = UserFaker::faker();

$userArray = $user->toArray();
// Content of $userArray:
// [
//    "name" => "John Doe"
//    "email" => "john.doe@example.com"
//    "userId" => "550e8400-e29b-41d4-a716-446655440000"
//    "phone" => "+1-555-123-4567"
//    "age" => 35
//    "isActive" => true
// ]
```