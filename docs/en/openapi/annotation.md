# Start Service

## [OpenApi] Add Property Description and Example Value

### Add Property Description and Example Value

```php
use Astral\Serialize\Serialize;
use Astral\Serialize\OpenApi\Annotations\OpenApi;

class UserAddRequest extends Serialize {

    #[OpenApi(description: 'this is name',example: 'Job')]
    public string $name;
    
    #[OpenApi(description: 'this is id',example: '1')]
    public int $id;
}
```

### Hide Input Property

Added `InputIgnore` annotation class. When generating OpenAPI docs for a `Request` class, these properties will be automatically ignored.

```php
use Astral\Serialize\Serialize;
use Astral\Serialize\OpenApi\Annotations\OpenApi;
use Astral\Serialize\Annotations\DataCollection\InputIgnore;

class UserAddRequest extends Serialize {
     
    #[InputIgnore]
    public object $admin;
        
    #[OpenApi(description: 'this is name',example: 'Job')]
    public string $name;
    
    #[OpenApi(description: 'this is id',example: '1')]
    public int $id;
}
```

### Hide Output Property

Added `OutputIgnore` annotation class. When generating OpenAPI docs for a `Response` class, these properties will be automatically ignored.

```php
use Astral\Serialize\Serialize;
use Astral\Serialize\OpenApi\Annotations\OpenApi;
use Astral\Serialize\Annotations\DataCollection\OutputIgnore;

class UserAddRequest extends Serialize {
     
    #[OutputIgnore]
    public object $admin;
        
    #[OpenApi(description: 'this is name')]
    public string $name;
    
    #[OpenApi(description: 'this is id')]
    public int $id;
}
```

tips: For detailed usage of `OutputIgnore` and `InputIgnore`, see [Property Ignore](../annotation/ignore-annotation.md)


## [Headers] Add/Remove Request Headers

* Add `user-token` with default value `true`
* Add `company-id` with default value `''`
* Remove `token` request header

```php
use Astral\Serialize\Serialize;

#[\Astral\Serialize\OpenApi\Annotations\Tag('user management')]
class UserController {

    #[\Astral\Serialize\OpenApi\Annotations\Summary('create user')]
    #[\Astral\Serialize\OpenApi\Annotations\Route('/user/create')]
    #[\Astral\Serialize\OpenApi\Annotations\Headers(headers:['user-token'=>'true','company-id'=>''], withOutHeaders: ['token'])]
    public function create() 
    {
        return new UserDto(); 
    }
    
  
}
```

## [Tag] Add Tag Description

Each Controller must add a `Tag` annotation for OpenAPI documentation to be generated correctly.
* `value` Tag name
* `description` Tag description
* `sort` Sort order. The higher the value, the earlier the tag appears.

```php
#[\Astral\Serialize\OpenApi\Annotations\Tag(value:'user management', description: 'user management description', sort: 0 )]
class UserController {}
```

## [Summary] API Method Description

* `value` Method name
* `description` Method description

```php
#[\Astral\Serialize\OpenApi\Annotations\Tag(value:'user management', description: 'user management description', sort: 0 )]
class UserController {
    #[\Astral\Serialize\OpenApi\Annotations\Summary('create user')]
    public function create() 
    {
        return new UserDto(); 
    }
}
```

## [Route] Route

A `Route` annotation class must exist for OpenAPI documentation to be generated correctly. The route address must be unique; duplicate addresses will cause inconsistent display.

* `route` Request path
* `method` Request method, default is POST 

```php
#[\Astral\Serialize\OpenApi\Annotations\Tag('user management')]
class UserController {
    #[\Astral\Serialize\OpenApi\Annotations\Summary('create user')]
    #[\Astral\Serialize\OpenApi\Annotations\Route('/user/create')]
    public function create() 
    {
        return new UserDto(); 
    }
}
```

## [RequestBody] API Method Description

### Implicitly Obtain RequestBody

When the parameter object of the current API inherits from the `Serialize` object, it will automatically be used as the `RequestBody`.

```php
use Astral\Serialize\Serialize;

class UserAddRequest extends Serialize {
    public string $name;
    public int $id;
}

#[\Astral\Serialize\OpenApi\Annotations\Tag('user management')]
class UserController {
    #[\Astral\Serialize\OpenApi\Annotations\Summary('create user')]
    #[\Astral\Serialize\OpenApi\Annotations\Route('/user/create')]
    public function create(UserAddRequest $request) 
    {
        return new UserDto(); 
    }
}
```

### RequestBody Grouped Documentation Display

When a group is specified in `RequestBody`, OpenAPI documentation will show the properties under that group.

```php
use Astral\Serialize\Serialize;
use Astral\Serialize\Attributes\Groups;

class UserAddRequest extends Serialize {
    [Groups('edit','add')]
    public string $name;
    
     [Groups('edit')]
    public int $id;
}

#[\Astral\Serialize\OpenApi\Annotations\Tag('user management')]
class UserController {
    #[\Astral\Serialize\OpenApi\Annotations\Summary('create user')]
    #[\Astral\Serialize\OpenApi\Annotations\Route('/user/create')]
    #[\Astral\Serialize\OpenApi\Annotations\RequestBody(groups: ['edit'])]
    public function edit(UserAddRequest $request) {}
}
```
Tips: For more details on Groups usage, please refer to [Attribute Grouping](../annotation/group-annotation.md)

## [Response] API Method Description

### Implicitly Obtain Response

When an object returns another object that inherits from `Serialize`, it will automatically be used as the `Response`.

```php
use Astral\Serialize\Serialize;

class UserResponse extends Serialize {
    public string $name;
    public int $id;
}


#[\Astral\Serialize\OpenApi\Annotations\Tag('user management')]
class UserController {
    #[\Astral\Serialize\OpenApi\Annotations\Summary('create user')]
    #[\Astral\Serialize\OpenApi\Annotations\Route('/user/create')]
    public function create(UserAddRequest $request): UserResponse
    {
        return new UserResponse(); 
    }
}
```

### Response Grouped Documentation Display

If a `Response` specifies a group, the OpenAPI documentation will display only the properties in that group.

```php
use Astral\Serialize\Serialize;
use Astral\Serialize\Attributes\Groups;

class UserResponse extends Serialize {
    [Groups('detail','guest')]
    public string $name;
    
     [Groups('detail')]
    public int $mobile;
}

#[\Astral\Serialize\OpenApi\Annotations\Tag('user management')]
class UserController {
    #[\Astral\Serialize\OpenApi\Annotations\Summary('create user')]
    #[\Astral\Serialize\OpenApi\Annotations\Route('/user/create')]
    #[\Astral\Serialize\OpenApi\Annotations\Response(className:UserAddRequest::class, groups: ['guest'])]
    public function edit($request) {}
}
```

Tips: For more details on Groups usage, please refer to [Attribute Grouping](../annotation/group-annotation.md)