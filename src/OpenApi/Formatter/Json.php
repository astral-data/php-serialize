<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Formatter;

class Json implements FormatterInterface
{
    public function output(string $path): bool
    {
        return true;
    }

    public function toString(): string
    {
        return '';
    }
}
