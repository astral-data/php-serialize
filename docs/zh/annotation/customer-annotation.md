### 自定义注解类实现

你可以通过自定义注解类，灵活地扩展序列化库的输入输出处理逻辑。

---

#### 入参处理注解类

实现 `InputValueCastInterface` 接口，重写其中的 `match` 和 `resolve` 方法，来自定义输入数据的转换和处理。

- **`match`**：用于判断是否对当前值进行处理，返回 `true` 表示进入 `resolve`。
- **`resolve`**：对匹配的输入值进行转换，并返回转换后的结果。

示例：给输入值添加自定义前缀的注解类

```php
use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class CustomerInput implements InputValueCastInterface
{
    public function __construct(
        public string $prefix = '',
    ) {
    }
    
    public function match(mixed $value, DataCollection $collection, InputValueContext $context): bool
    {
        // 对所有输入值都生效
        return true;
    }

    public function resolve(mixed $value, DataCollection $collection, InputValueContext $context): mixed
    {
        // 给输入值添加前缀
        return $this->prefix . $value;
    }
}
````

### 输出处理注解类

输出处理注解与输入处理注解类似，只是实现的接口不同——需要实现 `OutputValueCastInterface`，用以对序列化输出的值进行自定义转换。

示例：给序列化输出的值添加自定义后缀的注解类

```php
use Astral\Serialize\Contracts\Attribute\OutputValueCastInterface;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class CustomerOutput implements OutputValueCastInterface
{
    public function __construct(
        public string $suffix = '',
    ) {
    }
    
    public function match(mixed $value, DataCollection $collection, OutputValueContext $context): bool
    {
        // 对所有输出值都生效
        return true;
    }

    public function resolve(mixed $value, DataCollection $collection, OutputValueContext $context): mixed
    {
        // 给输出值添加后缀
        return $value . $this->suffix;
    }
}
```

