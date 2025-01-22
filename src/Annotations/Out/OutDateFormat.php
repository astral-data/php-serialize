<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations\Out;

use Astral\Serialize\Contracts\Attribute\OutValueCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Context\OutContext;
use Attribute;
use DateTime;
use DateTimeInterface;

/**
 * toArray 输出值为 固定日期格式 默认 YYYY-MM-DD HH:ii:ss的日期格式
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class OutDateFormat implements OutValueCastInterface
{
    public function __construct(public string $format = 'Y-m-d H:i:s')
    {
    }

    public function match(mixed $value, DataCollection $collection, OutContext $context): bool
    {
        return is_string($value) || is_numeric($value) || is_subclass_of($value, DateTimeInterface::class);
    }

    public function resolve(mixed $value, DataCollection $collection, OutContext $context): string|DateTime|null
    {
        return $this->formatValue($value);
    }

    private function formatValue(mixed $value): ?string
    {
        return match (true) {
            is_subclass_of($value, DateTimeInterface::class)        => $value->format($this->format),
            is_numeric($value)                                      => date($this->format, (int)$value),
            is_string($value) && strtotime($value) !== false        => date($this->format, strtotime($value)),
            default                                                 => null,
        };
    }
}
