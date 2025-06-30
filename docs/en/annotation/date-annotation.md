## 时间转换

1. 格式灵活性
   支持多种输入和输出时间格式
   可以轻松处理不同地区的日期表示
2. 时区处理
   支持在不同时区间转换
   自动处理时间的时区偏移
3. 类型安全
   自动将字符串转换为 DateTime 对象
   保证类型的一致性和正确性

### 基础使用

```php
use Astral\Serialize\Attributes\Input\InputDateFormat;
use Astral\Serialize\Attributes\Output\OutputDateFormat;
use Astral\Serialize\Serialize;

class TimeExample extends Serialize {

    // 输入时间格式转换
    #[InputDateFormat('Y-m-d')]
    public DateTime $dateTime;

    // 输入时间格式转换
    #[InputDateFormat('Y-m-d')]
    public string $dateDateString;

    // 输出时间格式转换
    #[OutputDateFormat('Y/m/d H:i')]
    public DateTime|string $processedAt;
    

    // 支持多种时间格式
    #[InputDateFormat('Y-m-d H:i:s')]
    #[OutputDateFormat('Y-m-d')]
    public string $createdAt;
}

// 创建订单对象
$order = TimeExample::from([
    'dateTime' => new DateTime('2023-08-11'),           // 输入格式：Y-m-d
    'dateDateString' => '2023-08-15',           // 输入格式：Y-m-d
    'processedAt' => '2023-08-16 14:30',   // 输入默认格式 也支持DateTime对象
    'createdAt' => '2023-08-16 14:45:30'   // 输入格式：Y-m-d H:i:s
]);

// 转换为数组
$orderArray = $order->toArray();
// $orderArray 的内容:
// [
//     'dateTime' => '2023-08-11',
//     ’dateDateString' => '2023-08-15',
//     'processedAt' => '2023/08/16 14:30',
//     'createdAt' => '2023-08-16'
// ]
```

### 带时区的时间转换

```php

use Astral\Serialize\Attributes\Input\InputDateFormat;
use Astral\Serialize\Attributes\Output\OutputDateFormat;
use Astral\Serialize\Serialize;

class AdvancedTimeUser extends Serialize {
    // 支持时区转换
    #[InputDateFormat('Y-m-d H:i:s', timezone: 'UTC')]
    #[OutputDateFormat('Y-m-d H:i:s', timezone: 'Asia/Shanghai')]
    public DateTime $registeredAt;

    // 支持不同地区的时间格式
    #[InputDateFormat('d/m/Y')]  // 英国格式
    #[OutputDateFormat('Y-m-d')]  // 标准格式
    public DateTime $birthDate;
}

// 使用高级时间转换
$advancedUser = AdvancedTimeUser::from([
    'registeredAt' => '2023-08-16 10:00:00',  // UTC 时间
    'birthDate' => '15/08/1990'  // 英国日期格式
]);

$advancedUserArray = $advancedUser->toArray();
// $advancedUserArray 的内容:
// [
//     'registeredAt' => '2023-08-16 18:00:00',  // 转换为上海时区
//     'birthDate' => '1990-08-15'
// ]
```