<?php

use Astral\Serialize\OpenApi;

require_once  dirname(__DIR__, 6) . '/vendor/autoload.php';

try {
    echo (new OpenApi())->handleByFolders()->toString();
} catch (JsonException|ReflectionException $e) {
    throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
}