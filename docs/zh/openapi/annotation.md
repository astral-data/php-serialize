# 启动服务

## [OpenApi] 添加属性说明 示例值

### 增加属性说明 示例值

```php
use Astral\Serialize\Serialize;
use Astral\Serialize\OpenApi\Annotations\OpenApi;

class UserAddRequest extends Serialize {

    #[OpenApi(description: '姓名',example: '张三')]
    public string $name;
    
    #[OpenApi(description: 'ID',example: '1')]
    public int $id;
}
```

### 隐藏输入属性

添加了 `InputIgnore` 注解类 作为`Request`类 openapi 生成文档时会自动忽略

```php
use Astral\Serialize\Serialize;
use Astral\Serialize\OpenApi\Annotations\OpenApi;
use Astral\Serialize\Annotations\DataCollection\InputIgnore;

class UserAddRequest extends Serialize {
     
    #[InputIgnore]
    public object $admin;
        
    #[OpenApi(description: '姓名',example: '张三')]
    public string $name;
    
    #[OpenApi(description: 'ID',example: '1')]
    public int $id;
}
```

### 隐藏输出属性

添加了 `OutputIgnore` 注解类 作为`Response`类 openapi 生成文档时会自动忽略

```php
use Astral\Serialize\Serialize;
use Astral\Serialize\OpenApi\Annotations\OpenApi;
use Astral\Serialize\Annotations\DataCollection\OutputIgnore;

class UserAddRequest extends Serialize {
     
    #[OutputIgnore]
    public object $admin;
        
    #[OpenApi(description: '姓名')]
    public string $name;
    
    #[OpenApi(description: 'ID')]
    public int $id;
}
```

tips: `OutputIgnore` 和 `InputIgnore` 的详细使用请查看 [属性忽略](../annotation/ignore-annotation.md)


## [Headers] 添加/剔除请求头

* 增加 `user-token` 设置默认值 `true`
* 增加 `company-id` 设置默认值 `''`
* 删除 `token` 请求头

```php
use Astral\Serialize\Serialize;

#[\Astral\Serialize\OpenApi\Annotations\Tag('用户模块管理')]
class UserController {

    #[\Astral\Serialize\OpenApi\Annotations\Summary('创建用户')]
    #[\Astral\Serialize\OpenApi\Annotations\Route('/user/create')]
    #[\Astral\Serialize\OpenApi\Annotations\Headers(headers:['user-token'=>'true','company-id'=>''], withOutHeaders: ['token'])]
    public function create() 
    {
        return new UserDto(); 
    }
    
  
}
```

## [Tag] 添加栏目说明

每一个Controller必须添加`Tag`注解类才会正常生成openapi文档
* `value` 栏目名称
* `description` 栏目说明
* `sort` 排序 值越大 栏目排序越靠前

```php
#[\Astral\Serialize\OpenApi\Annotations\Tag(value:'用户模块管理', description: '说明文案', sort: 0 )]
class UserController {}
```

## [Summary] 接口方法说明

* `value` 方法名称
* `description` 方法介绍

```php
#[\Astral\Serialize\OpenApi\Annotations\Tag(value:'用户模块管理', description: '说明文案', sort: 0 )]
class UserController {
    #[\Astral\Serialize\OpenApi\Annotations\Summary('创建用户')]
    public function create() 
    {
        return new UserDto(); 
    }
}
```

## [Route] 路由

必须存在`Route`注解类才会正常生成openapi文档 同时需要保证 路由地址唯一 如果地址重复会导致显示不一致

* `route` 求情路径
* `method` 请求方法 默认POST 

```php
#[\Astral\Serialize\OpenApi\Annotations\Tag('用户模块管理')]
class UserController {
    #[\Astral\Serialize\OpenApi\Annotations\Summary('创建用户')]
    #[\Astral\Serialize\OpenApi\Annotations\Route('/user/create')]
    public function create() 
    {
        return new UserDto(); 
    }
}
```

## [RequestBody] 接口方法说明

### 隐式获取 RequestBody

当前接口入参对象 继承了 `Serialize`对象时，会自动获取该对象作为 `RequestBody`

```php
use Astral\Serialize\Serialize;

class UserAddRequest extends Serialize {
    public string $name;
    public int $id;
}

#[\Astral\Serialize\OpenApi\Annotations\Tag('用户模块管理')]
class UserController {
    #[\Astral\Serialize\OpenApi\Annotations\Summary('创建用户')]
    #[\Astral\Serialize\OpenApi\Annotations\Route('/user/create')]
    public function create(UserAddRequest $request) 
    {
        return new UserDto(); 
    }
}
```

### RequestBody Group分组显示文档

`RequestBody` 指定了 group openapi 生成文档会显示该分组下的属性

```php
use Astral\Serialize\Serialize;
use Astral\Serialize\Attributes\Groups;

class UserAddRequest extends Serialize {
    [Groups('edit','add')]
    public string $name;
    
     [Groups('edit')]
    public int $id;
}

#[\Astral\Serialize\OpenApi\Annotations\Tag('用户模块管理')]
class UserController {
    #[\Astral\Serialize\OpenApi\Annotations\Summary('创建用户')]
    #[\Astral\Serialize\OpenApi\Annotations\Route('/user/create')]
    #[\Astral\Serialize\OpenApi\Annotations\RequestBody(groups: ['edit'])]
    public function edit(UserAddRequest $request) {}
}
```
tips: Groups 详细使用请查看 [属性分组](../annotation/group-annotation.md)

## [Response] 接口方法说明

### 隐式获取 Response

当对象返回对象 继承了 `Serialize`对象时，会自动获取该对象作为 `Response`

```php
use Astral\Serialize\Serialize;

class UserResponse extends Serialize {
    public string $name;
    public int $id;
}


#[\Astral\Serialize\OpenApi\Annotations\Tag('用户模块管理')]
class UserController {
    #[\Astral\Serialize\OpenApi\Annotations\Summary('创建用户')]
    #[\Astral\Serialize\OpenApi\Annotations\Route('/user/create')]
    public function create(UserAddRequest $request): UserResponse
    {
        return new UserResponse(); 
    }
}
```

### Response Group分组显示文档

`Response` 指定了 group openapi 生成文档会显示该分组下的属性

```php
use Astral\Serialize\Serialize;
use Astral\Serialize\Attributes\Groups;

class UserResponse extends Serialize {
    [Groups('detail','guest')]
    public string $name;
    
     [Groups('detail')]
    public int $mobile;
}

#[\Astral\Serialize\OpenApi\Annotations\Tag('用户模块管理')]
class UserController {
    #[\Astral\Serialize\OpenApi\Annotations\Summary('创建用户')]
    #[\Astral\Serialize\OpenApi\Annotations\Route('/user/create')]
    #[\Astral\Serialize\OpenApi\Annotations\Response(className:UserAddRequest::class, groups: ['guest'])]
    public function edit($request) {}
}
```

tips: Groups 详细使用请查看 [属性分组](../annotation/group-annotation.md)