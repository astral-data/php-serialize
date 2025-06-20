<?php

use Astral\Serialize\OpenApi\Handler\Config;
use Astral\Serialize\OpenApi;

require_once Config::rootPath().'/vendor/autoload.php';

try {
    echo (new OpenApi())->handleByFolders()->toString();
} catch (JsonException|ReflectionException $e) {
    throw new RuntimeException($e->getMessage(),$e->getCode(),$e);
}