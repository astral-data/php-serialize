## 参数校验

### 使用 validate 方法进行参数校验

`Serialize` 类提供了一个 `validate` 方法，当通过 `from` 方法创建对象时，该方法会被自动调用。我们可以在 `validate` 方法中实现自定义的数据验证逻辑。

下面是一个使用示例：

```php
use Astral\Serialize\Serialize;

class TestConstructValidationFromSerialize extends Serialize 
{
    public string $type_string;

    /**
     * 数据验证方法
     * 在对象通过 from 方法创建后自动调用
     */
    public function validate(): void
    {
        // 验证 type_string 属性的值
        if ($this->type_string !== '123') {
            throw new Exception('type_string 必须等于 123');
        }

        // 可以修改属性值
        $this->type_string = '234';
    }
}
```

### 在构造函数中进行参数校验

当直接使用 `__construct` 方法创建对象时，可以在构造函数中实现参数验证逻辑。但需要注意的是，这种方式只能访问构造函数中定义的属性，无法访问其他属性。

```php
use Astral\Serialize\Serialize;

class TestConstructFromSerialize extends Serialize
{
    // 注意：这个属性在构造函数中无法访问
    // 如果需要验证此属性，请使用 validate 方法
    public string $not_validate_string;
    
    /**
     * 构造函数中的参数验证
     * @param string $type_string 输入的字符串参数
     */
    public function __construct(
        public string $type_string,
    ) {
        // 验证传入的参数
        if ($this->type_string !== '123') {
            throw new Exception('type_string 必须等于 123');
        }

        // 修改属性值
        $this->type_string = '234';
    }
}