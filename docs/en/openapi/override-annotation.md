# Override Annotation

`Route::class`, `Summary::class`, `RequestBody::class`, `Response::class`, and `Headers::class` annotations can be overridden according to your business needs.

## Override Route Annotation

Override the Route annotation to add `withOutMiddleware` and `withMiddleware` properties.

```php
#[Attribute(Attribute::TARGET_METHOD)]
    class CustomerRoute extends OpenApi\Annotations\Route
    {
         public function __construct(
            public string $route,
            public MethodEnum $method = MethodEnum::POST,
            public array $withOutMiddleware = [],
            public array $withMiddleware = [],
        ) {
        }
    }

    #[\Astral\Serialize\OpenApi\Annotations\Tag('user management')]
    class UserController {
    
        #[\Astral\Serialize\OpenApi\Annotations\Summary('create user')]
        // Using a custom annotation can also be recognized and generate JSON.
        #[CustomerRoute('/user/create', withMiddleware:['auth'])]  
        public function create() 
        {
            return new UserDto(); 
        }
        
      
    }
```
