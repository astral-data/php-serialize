<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Handler;

 use Astral\Serialize\OpenApi\Collections\OpenApiCollection;
use Astral\Serialize\OpenApi\Storage\OpenAPI\ApiInfo;
use Astral\Serialize\OpenApi\Storage\OpenAPI\OpenAPI;
use Astral\Serialize\OpenApi\Storage\OpenAPI\ParameterStorage;
use Astral\Serialize\OpenApi\Storage\OpenAPI\SchemaStorage;
use Exception;
use JsonException;
use ReflectionException;

class Config
{

    public static $config;

    public static function rootPath(): string
    {
        return dirname(__DIR__, 3);
    }

    public static function build()
    {
        if(self::$config){
            return self::$config;
        }

        $path = self::rootPath().'/.openapi.php';
        if(is_file($path)){
            self::$config = include $path;
        }
        else{
            self::$config = include dirname(__DIR__, 3).'/.openapi.php';
        }

        return self::$config;
    }

    public static function get($key)
    {
        return self::build()[$key] ?? '';
    }

}
