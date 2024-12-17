<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations\InputValue;

use Astral\Serialize\Contracts\Attribute\InputValueCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Attribute;
use DateTime;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class InputDataFormat implements InputValueCastInterface
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

    public function resolve(mixed $value, DataCollection $dataCollection): string
    {
        $dateTime = DateTime::createFromFormat($this->inputFormat, (string)$value);
        return $dateTime !== false ? $dateTime->format($this->outFormat) : (string)$value;
    }
}
