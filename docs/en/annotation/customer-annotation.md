### Custom Annotation Class Implementation

You can flexibly extend the input/output handling logic of the serialization library by defining custom annotation classes.

---

#### Input Processing Annotation Class

Implement the `InputValueCastInterface` interface, and override its `match` and `resolve` methods to customize the conversion and handling of input data.

- **`match`**: Used to determine whether to process the current value; returning `true` means `resolve` will be called.
- **`resolve`**: Converts the matched input value and returns the result.

Example: Annotation class that adds a custom prefix to the input value

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
        // Applies to all input values
        return true;
    }

    public function resolve(mixed $value, DataCollection $collection, InputValueContext $context): mixed
    {
        // Add prefix to input value
        return $this->prefix . $value;
    }
}
```

### Output Processing Annotation Class

The output processing annotation is similar to the input processing annotation, but implements a different interface—`OutputValueCastInterface`, which is used to customize the conversion of serialized output values.

Example: Annotation class that adds a custom suffix to the serialized output value

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
        // Applies to all output values
        return true;
    }

    public function resolve(mixed $value, DataCollection $collection, OutputValueContext $context): mixed
    {
        // Add suffix to output value
        return $value . $this->suffix;
    }
}
```

