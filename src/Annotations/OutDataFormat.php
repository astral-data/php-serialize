<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations;

use Attribute;

/**
 * toArray 输出值为 固定日期格式 默认 YYYY-MM-DD HH:ii:ss的日期格式
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class OutDataFormat
{
    /** @var string 日期格式 */
    public string $format = 'Y-m-d H:i:s';

    public bool $isThrow = false;

    /**
     * Undocumented function
     *
     * @param  string  $format  输出的日期格式
     * @param  bool  $isThrow  非时间格式是否抛出异常 默认抛出
     * @return void
     */
    public function __construct(string $format, bool $isThrow = false)
    {
        $this->format = $format;
        $this->isThrow = $isThrow;
    }
}
