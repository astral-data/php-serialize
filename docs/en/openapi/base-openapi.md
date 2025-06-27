## Creating Request

```php
use Astral\Serialize\Serialize;

class UserAddRequest extends Serialize {
    public string $name;
    public int $id;
}

class UserDetailRequest extends Serialize {
    public int $id;
}
```

## Creating Response
```php
use Astral\Serialize\Serialize;

class UserDto extends Serialize {
    public string $name,
    public int $id;
}
```

## Creating Controller
```php
use Astral\Serialize\Serialize;
use Astral\Serialize\OpenApi\Enum\MethodEnum;

#[\Astral\Serialize\OpenApi\Annotations\Tag('User Module Management')]
class UserController {

    #[\Astral\Serialize\OpenApi\Annotations\Summary('Create User')]
    #[\Astral\Serialize\OpenApi\Annotations\Route('/user/create')]
    #[\Astral\Serialize\OpenApi\Annotations\RequestBody(UserAddRequest::class)]
     #[\Astral\Serialize\OpenApi\Annotations\Response(UserDto::class)]
    public function create() 
    {
        return new UserDto(); 
    }
    
    #[\Astral\Serialize\OpenApi\Annotations\Summary('User Detail')]
    #[\Astral\Serialize\OpenApi\Annotations\Route(route:'/user/detail', method: MethodEnum::GET)]
    public function detail(UserDetailRequest $request): UserDto  
    {
        return new UserDto();
    }
}
```
## Starting the Service

### Docker Deployment

Navigate to the project root directory first:

```shell
docker run  -v $PWD/vendor/astral/php-serialize/src/OpenApi/Frankenphp/Caddyfile:/etc/frankenphp/Caddyfile -v $PWD:/app -p 8089:80 dunglas/frankenphp
```
Access `http://127.0.0.1:8089/docs` to view the documentation.

![UI-IMG](./ui.png)