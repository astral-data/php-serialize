## Collection Faker

```php

class UserProfile extends Serialize {
    public string $nickname;
    public int $age;
    public string $email;
    public string $avatar;
}

class UserListFaker extends Serialize {
    #[FakerCollection(['name', 'email'], num: 3)]
    public array $users;

     #[FakerCollection(UserProfile::class, num: 2)]
    public array $profiles;
}

$userList = UserListFaker::faker();

$complexUserListFaker = UserListFaker::faker();

$complexUserListFakerArray = $complexUserListFaker->toArray();
// Content of $complexUserListFakerArray:
// [
//     'profile' => [
//        [0] => UserProfile Object (
//              [
//              'nickname' => 'RandomNickname', 
//              'age' => 28, 'email' => 'random.user@example.com', 
//              'avatar' => 'https://example.com/avatars/random-avatar.jpg'
//              ],
//         ),
//         [1] => UserProfile Object (
//              [
//              'nickname' => 'RandomNickname', 
//              'age' => 28, 'email' => 'random.user@example.com', 
//              'avatar' => 'https://example.com/avatars/random-avatar.jpg'
//              ],
//         )
//      ],  
//     'users' => [
//         ['name' => 'RandomNickname', 'email' => 'RandomEmail@example.com']
//         ['name' => 'RandomNickname', 'email' => 'RandomEmail@example.com']
//         ['name' => 'RandomNickname', 'email' => 'RandomEmail@example.com']
//     ]
// ]
```
