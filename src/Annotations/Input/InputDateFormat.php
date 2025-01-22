<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations\Input;

use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Context\InputValueContext;
use Attribute;
use DateTime;
use DateTimeInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class InputDateFormat implements InputValueCastInterface
{
    public string $inputFormat;

    public ?string $outFormat;

    /**
     * @param string $inputFormat
     * @param string|null $outFormat
     */
    public function __construct(string $inputFormat = 'Y-m-d H:i:s', ?string $outFormat = null)
    {
        $this->inputFormat = $inputFormat;
        $this->outFormat   = $outFormat;
    }

    public function match(mixed $value, DataCollection $collection, InputValueContext $context): bool
    {
        return is_string($value) || is_numeric($value);
    }

    public function resolve(mixed $value, DataCollection $collection, InputValueContext $context): string|DateTime
    {
        if (!$this->outFormat
            && count($collection->getTypes()) === 1
            && is_subclass_of(current($collection->getTypes())?->className, DateTimeInterface::class)
        ) {
            return  (current($collection->getTypes())?->className)::createFromFormat($this->inputFormat, (string)$value);
        }

        $dateTime = DateTime::createFromFormat($this->inputFormat, (string)$value);
        return $dateTime !== false ? $dateTime->format($this->outFormat) : (string)$value;
    }
}
