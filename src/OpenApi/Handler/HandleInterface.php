<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Handler;

interface HandleInterface
{
    public function output(string $path): bool;

    public function toString(): string;
}
