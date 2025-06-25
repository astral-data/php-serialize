<?php

use Astral\Serialize\OpenApi;
use Astral\Serialize\OpenApi\Handler\Config;

require_once Config::rootPath() . '/vendor/autoload.php';

try {
    echo (new OpenApi())->handleByFolders()->toString();
} catch (JsonException|ReflectionException $e) {
    throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
}
