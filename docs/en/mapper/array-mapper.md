## Array Object Mapping

### phpDoc Definition

```php
use Astral\Serialize\Serialize;

// Define base array types
class ArrayOne extends Serialize {
    public string $type = 'one';
    public string $name;
}

class ArrayTwo extends Serialize {
    public string $type = 'two';
    public string $code;
}

class MultiArraySerialize extends Serialize {
    // Scenario 1: Mixed type array
    /** @var (ArrayOne|ArrayTwo)[] */
    public array $mixedTypeArray;

    // Scenario 2: Multiple type array
    /** @var ArrayOne[]|ArrayTwo[] */
    public array $multiTypeArray;

    // Scenario 3: Key-value mixed type
    /** @var array(string, ArrayOne|ArrayTwo) */
    public array $keyValueMixedArray;
}

// Scenario 1: Mixed type array
$data1 = MultiArraySerialize::from(
    mixedTypeArray : [
        ['name' => 'Job'],           //  Convert to ArrayOne object
        ['code' => 'ABC123'],         // Convert to ArrayTwo object
        ['name' => 'Lin'],            // Convert to ArrayOne object
        ['code' => 'DEF456']          // Convert to ArrayTwo object
    ]
);

var_dump($data1)
// Content of $data1:
// [
//     'mixedTypeArray' => [
//           [0] => ArrayOne Object
//                (
//                    ['name' => 'Job', 'type' => 'one'],
//                )
//           [1] => ArrayTwo Object
//                (
//                    ['code' => 'ABC123', 'type' => 'two'],
//                )
//           [2] => ArrayOne Object
//                (
//                    ['name' => 'Lin', 'type' => 'one'],
//                )
//           [3] => ArrayTwo Object
//                (
//                    ['code' => 'DEF456', 'type' => 'two'],
//                )
//     ]
// ]

// Scenario 2: Multi-type array
$data2 = MultiArraySerialize::from(
    multiTypeArray:[
        ['name' => 'Tom'],            // Convert to ArrayOne object
        ['name' => 'Joy'],            // Convert to ArrayOne object
        ['code' => 'GHI789']          // Convert to ArrayTwo object
    ]
);

var_dump($data2)
// Content of $data2:
// [
//     'multiTypeArray' => [
//         ArrayOne Object (
//             ['name' => 'Tom', 'type' => 'one']
//         ),
//         ArrayOne Object (
//             ['name' => 'Joy', 'type' => 'one']
//         ),
//         ArrayTwo Object (
//             ['code' => 'GHI789', 'type' => 'two']
//         )
//     ]
// ]

// Scenario 3: Key-value mixed type array
$data3 = MultiArraySerialize::from(
    keyValueMixedArray: [
        'user1' => ['name' => 'Joy'],           // Convert to ArrayOne object
        'system1' => ['code' => 'ABC123'],       // Convert to ArrayTwo object
        'user2' => ['name' => 'Tom']            // Convert to ArrayOne object
    ]
);

var_dump($data3)
// Content of $data3:
// [
//     'keyValueMixedArray' => [
//         'user1' => ArrayOne Object (
//             ['name' => 'Joy', 'type' => 'one']
//         ),
//         'system1' => ArrayTwo Object (
//             ['code' => 'ABC123', 'type' => 'two']
//         ),
//         'user2' => ArrayOne Object (
//             ['name' => 'Tom', 'type' => 'one']
//         )
//     ]
// ]

// Scenario 4: Handling when no match is found
$data4 = MultiArraySerialize::from(
    mixedTypeArray : [
        ['unknown' => 'data1'],
        ['another' => 'data2']
    ]
);

var_dump($data4)
// Content of $data4:
// [
//     'mixedTypeArray' => [
//         ['unknown' => 'data1'],
//         ['another' => 'data2']
//     ]
// ]
```