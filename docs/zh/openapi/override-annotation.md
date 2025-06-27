# 重写注解

`Route::class` `Summary::class` `RequestBody::class` `Response::class` `Headers::class` 可以根据自身业务重写注解

## 重写 Route 注解

重写Route注解 增加了 `withOutMiddleware` `withMiddleware` 属性

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

    #[\Astral\Serialize\OpenApi\Annotations\Tag('用户模块管理')]
    class UserController {
    
        #[\Astral\Serialize\OpenApi\Annotations\Summary('创建用户')]
        #[CustomerRoute('/user/create', withMiddleware:['auth'])]  // 使用自定义注解也能识别生成json
        public function create() 
        {
            return new UserDto(); 
        }
        
      
    }
```
