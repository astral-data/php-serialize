<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations\Input;

use DateTimeZone;
use DateInvalidTimeZoneException;
use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Context\InputValueContext;
use Attribute;
use DateTime;
use DateTimeInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class InputDateFormat implements InputValueCastInterface
{
    public function __construct(
        public string $inputFormat = 'Y-m-d H:i:s',
        public ?string $outFormat = null,
        public ?string $timezone = null,
    ) {

    }

    public function match(mixed $value, DataCollection $collection, InputValueContext $context): bool
    {
        return is_string($value) || is_numeric($value);
    }

    /**
     * @throws DateInvalidTimeZoneException
     */
    public function resolve(mixed $value, DataCollection $collection, InputValueContext $context): string|DateTime
    {
        $timezone = $this->timezone ? new DateTimeZone($this->timezone) : null;

        if (!$this->outFormat
            && count($collection->getTypes()) === 1
            && is_subclass_of(current($collection->getTypes())?->className, DateTimeInterface::class)
        ) {
            return  (current($collection->getTypes())?->className)::createFromFormat($this->inputFormat, (string)$value, $timezone);
        }

        $dateTime = DateTime::createFromFormat($this->inputFormat, (string)$value, $timezone);
        return $dateTime !== false ? $dateTime->format($this->outFormat) : (string)$value;
    }
}
