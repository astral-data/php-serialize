#### 数组对象转换

##### phpDoc定义

```php
use Astral\Serialize\Serialize;

// 定义基础数组类型
class ArrayOne extends Serialize {
    public string $type = 'one';
    public string $name;
}

class ArrayTwo extends Serialize {
    public string $type = 'two';
    public string $code;
}

class MultiArraySerialize extends Serialize {
    // 场景1：混合类型数组
    /** @var (ArrayOne|ArrayTwo)[] */
    public array $mixedTypeArray;

    // 场景2：多类型数组
    /** @var ArrayOne[]|ArrayTwo[] */
    public array $multiTypeArray;

    // 场景3：键值对混合类型
    /** @var array(string, ArrayOne|ArrayTwo) */
    public array $keyValueMixedArray;
}

// 场景1：混合类型数组
$data1 = MultiArraySerialize::from(
    mixedTypeArray : [
        ['name' => '张三'],           //  转化 ArrayOne 对象
        ['code' => 'ABC123'],         // 转化 ArrayTwo 对象
        ['name' => '李四'],            // 转化 ArrayOne 对象
        ['code' => 'DEF456']          // 转化 ArrayTwo 对象
    ]
);

$data1Array = $data1->toArray();
// $data1Array 的内容:
// [
//     'mixedTypeArray' => [
//           [0] => ArrayOne Object
//                (
//                    ['name' => '张三', 'type' => 'one'],
//                )
//           [1] => ArrayTwo Object
//                (
//                    ['code' => 'ABC123', 'type' => 'two'],
//                )
//           [2] => ArrayOne Object
//                (
//                    ['name' => '李四', 'type' => 'one'],
//                )
//           [3] => ArrayTwo Object
//                (
//                    ['code' => 'DEF456', 'type' => 'two'],
//                )
//     ]
// ]

// 场景2：多类型数组
$data2 = MultiArraySerialize::from(
    multiTypeArray:[
        ['name' => '王五'],            // 转化 ArrayOne 对象
        ['name' => '赵六'],            // 转化 ArrayOne 对象
        ['code' => 'GHI789']          // 转化 ArrayTwo 对象
    ]
);

$data2Array = $data2->toArray();
// $data2Array 的内容:
// [
//     'multiTypeArray' => [
//         ArrayOne Object (
//             ['name' => '王五', 'type' => 'one']
//         ),
//         ArrayOne Object (
//             ['name' => '赵六', 'type' => 'one']
//         ),
//         ArrayTwo Object (
//             ['code' => 'GHI789', 'type' => 'two']
//         )
//     ]
// ]

// 场景3：键值对混合类型
$data3 = MultiArraySerialize::from(
    keyValueMixedArray: [
        'user1' => ['name' => '张三'],           // 转化 ArrayOne 对象
        'system1' => ['code' => 'ABC123'],       // 转化 ArrayTwo 对象
        'user2' => ['name' => '李四']            // 转化 ArrayOne 对象
    ]
);

$data3Array = $data3->toArray();
// $data3Array 的内容:
// [
//     'keyValueMixedArray' => [
//         'user1' => ArrayOne Object (
//             ['name' => '张三', 'type' => 'one']
//         ),
//         'system1' => ArrayTwo Object (
//             ['code' => 'ABC123', 'type' => 'two']
//         ),
//         'user2' => ArrayOne Object (
//             ['name' => '李四', 'type' => 'one']
//         )
//     ]
// ]

// 场景4：无法匹配时的处理
$data4 = MultiArraySerialize::from(
    mixedTypeArray : [
        ['unknown' => 'data1'],
        ['another' => 'data2']
    ]
);

$data4Array = $data4->toArray();
// $data4Array 的内容:
// [
//     'mixedTypeArray' => [
//         ['unknown' => 'data1'],
//         ['another' => 'data2']
//     ]
// ]
```