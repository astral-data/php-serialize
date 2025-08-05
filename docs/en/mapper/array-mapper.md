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

$data1Array = $data1->toArray();
// Content of $data1Array:
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

$data2Array = $data2->toArray();
// Content of $data2Array:
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

$data3Array = $data3->toArray();
// $data3Array toArray:
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

$data4Array = $data4->toArray();
// $data4Array toArray:
// [
//     'mixedTypeArray' => [
//         ['unknown' => 'data1'],
//         ['another' => 'data2']
//     ]
// ]
```