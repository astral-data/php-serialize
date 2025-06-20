<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Formatter;

interface FormatterInterface
{
    public function output(string $path): bool;

    public function toString(): string;
}
