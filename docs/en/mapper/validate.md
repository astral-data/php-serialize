## Parameter Validation

### Validation via the `validate` Method

The `Serialize` class provides a `validate` method that is automatically called when an object is created using the `from` method. You can implement custom data validation logic within this method.

Here's an example:

```php
use Astral\Serialize\Serialize;

class TestConstructValidationFromSerialize extends Serialize 
{
    public string $type_string;

    /**
     * Data validation method
     * Automatically called after object creation via the from method
     */
    public function validate(): void
    {
        // Validate the value of the type_string property
        if ($this->type_string !== '123') {
            throw new Exception('type_string must be equal to 123');
        }

        // You can also modify property values
        $this->type_string = '234';
    }
}
```

### Validation in `__construct`

When creating objects directly using the `__construct` method, you can implement parameter validation logic in the constructor. However, note that this approach can only access properties defined in the constructor and cannot access other properties.

```php
use Astral\Serialize\Serialize;

class TestConstructFromSerialize extends Serialize
{
    // Note: This property cannot be accessed in the constructor
    // If you need to validate this property, use the validate method
    public string $not_validate_string;
    
    /**
     * Parameter validation in the constructor
     * @param string $type_string The input string parameter
     */
    public function __construct(
        public string $type_string,
    ) {
        // Validate the input parameter
        if ($this->type_string !== '123') {
            throw new Exception('type_string must be equal to 123');
        }

        // Modify the property value
        $this->type_string = '234';
    }
}