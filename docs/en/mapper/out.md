## Output Format

### Creating the Serialize Class

```php
use Astral\Serialize\Serialize;
use DateTime;

class UserLoginLog extends Serialize {
    public string $remark,
    public DateTime $create_time;
}

class User extends Serialize {
    public string $name,
    public int $age,
    public UserLoginLog $login_log
}

// Create an object
$user = User::from([
    'name' => 'Jon',
    'age' => 30
], login_log: new UserLoginLog(remark:'Test Data',create_time: DateTime::createFromFormat('Y-m-d','2008-09-01')));
````

### Outputting the Object

```php
// $user is an object by default
echo $user->name;  // Output: Jon
echo $user->age;   // Output: 30
echo $user->login_log->remark; // Output 'Test Data'
echo $user->login_log->create_time; // Output DateTime Object

```

### Outputting an Array

```php
// Convert to an array
$vols = $user->toArray();
echo $vols['name'];  // Output: Jon
echo $vols['age'];   // Output: 30
echo $vols['login_log']['remark']; // Output 'Test Data'
echo $vols['login_log']['create_time']; // Output 2008-09-01 00:00:00
// Content of $vols:
// [
//     'name' => 'Jon',
//     'age' => 30,
//     'login_log' => [
//         [
//              'remark' => 'Test Data',
//              'create_time' => '2008-09-01 00:00:00'
//          ]
//     ]
// ]
```

### Outputting a JSON String

1. The `Serialize` class implements `JsonSerializable` by default. Similar to a `Laravel` `Controller` you can directly return the object, and the framework will output the `JSON` information correctly
2. By default, the JSON output from `Serialize` includes `data` `code` and  `message` f you need to [replace/modify/add] these, please refer to the configuration information [Response Data Structure Definition](../openapi/config.md)

#### Outputting JSON Information

- You can use the API `toJsonString` 
- Alternatively, you can directly use `json_decode`

```php
echo $user->toJsonString();
echo json_decode($user);
// Both outputs are the same
// {"code":200,"message":"success","data":{"name":"Jon","age":30,"login_log":{"remark":"Test Data","create_time":"2008-09-01 00:00:00"}}
```

#### Setting Output Code/Message

```php
$user->setCode(500);
$user->setMessage('Operation failed');
echo json_decode($user);
// Output
// {"code":500,"message":"Operation failed","data":{"name":"Jon","age":30,"login_log":{"remark":"Test Data","create_time":"2008-09-01 00:00:00"}}
```

#### Setting Custom JSON Outer Layer

`withResponses` can temporarily add or modify custom return information. To add global return information, you can configure it in the [Response Data Structure Definition](../openapi/config.md)

```php
$user->withResponses([
        "code"=> ["description"=>"Return Code", "value"=>401],
        "message"=> ["description"=>"Return Message", "value"=>"Operation successful"],
        "error"=> ["description"=>"Return Error", "value"=>0],
]);
// Output
// {"code":401,"message":"Operation successful","error":0,"data":{"name":"Jon","age":30,"login_log":{"remark":"Test Data","create_time":"2008-09-01 00:00:00"}}
```

#### Outputting JSON Without Outer Layer Information

Use`withoutResponseToJsonString` to return JSON data containing only the object’s properties.

```php
$user->withoutResponseToJsonString();
// Output
// {"name":"Jon","age":30,"login_log":{"remark":"Test Data","create_time":"2008-09-01 00:00:00"}
```