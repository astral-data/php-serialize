<?php

declare(strict_types=1);

namespace Astral\Serialize\Annotations\Output;

use Astral\Serialize\Contracts\Attribute\OutValueCastInterface;
use Astral\Serialize\Support\Collections\DataCollection;
use Astral\Serialize\Support\Context\OutContext;
use Attribute;
use DateInvalidTimeZoneException;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Exception;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_CLASS)]
class OutputDateFormat implements OutValueCastInterface
{
    public function __construct(
        public string $format = 'Y-m-d H:i:s',
        public ?string $timezone = null
    ) {
    }

    public function match(mixed $value, DataCollection $collection, OutContext $context): bool
    {
        return is_string($value) || is_numeric($value) || is_subclass_of($value, DateTimeInterface::class);
    }

    public function resolve(mixed $value, DataCollection $collection, OutContext $context): string|DateTime|null
    {
        return $this->formatValue($value);
    }

    /**
     * @throws DateInvalidTimeZoneException
     */
    private function formatValue(mixed $value): ?string
    {
        $timezone = $this->timezone ? new DateTimeZone($this->timezone) : null;

        return match (true) {
            is_subclass_of($value, DateTimeInterface::class) => $this->formatDateTime($value, $timezone),
            is_numeric($value)                               => $this->formatTimestamp((int)$value, $timezone),
            is_string($value) && strtotime($value) !== false => $this->formatStringDate($value, $timezone),
            default                                          => null,
        };
    }

    /**
     * @throws Exception
     */
    private function formatDateTime(DateTimeInterface $dateTime, ?DateTimeZone $timezone): string
    {
        if ($timezone) {
            $dateTime = (new DateTime($dateTime->format('Y-m-d H:i:s')))->setTimezone($timezone);
        }

        return $dateTime->format($this->format);
    }

    /**
     * @throws Exception
     */
    private function formatTimestamp(int $timestamp, ?DateTimeZone $timezone): string
    {
        $dateTime = new DateTime('@' . $timestamp); // Create DateTime from timestamp
        if ($timezone) {
            $dateTime->setTimezone($timezone);
        }

        return $dateTime->format($this->format);
    }

    /**
     * @throws Exception
     */
    private function formatStringDate(string $value, ?DateTimeZone $timezone): string
    {
        $dateTime = new DateTime($value);
        if ($timezone) {
            $dateTime->setTimezone($timezone);
        }

        return $dateTime->format($this->format);
    }
}
