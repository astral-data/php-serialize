<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations\InputValue;

use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Context\InputValueContext;
use Attribute;
use DateTime;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class InputDateFormat implements InputValueCastInterface
{
    /** @var string input-format */
    public string $inputFormat;

    /** @var string out-format */
    public string $outFormat;

    /**
     * @param  string  $inputFormat
     * @param  string  $outFormat
     */
    public function __construct(string $inputFormat = 'Y-m-d H:i:s', string $outFormat = 'Y-m-d H:i:s')
    {
        $this->inputFormat = $inputFormat;
        $this->outFormat   = $outFormat;
    }

    public function match(mixed $value, DataCollection $collection, InputValueContext $context): bool
    {
        return is_string($value) || is_numeric($value);
    }

    public function resolve(mixed $value, DataCollection $collection, InputValueContext $context): string
    {
        $dateTime = DateTime::createFromFormat($this->inputFormat, (string)$value);
        return $dateTime !== false ? $dateTime->format($this->outFormat) : (string)$value;
    }
}
