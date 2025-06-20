<?php

declare(strict_types=1);

namespace Astral\Serialize\OpenApi\Storage\OpenAPI;

use Astral\Serialize\OpenApi\Storage\StorageInterface;

/**
 * 文档开发者介绍
 */
class ContentProperties implements StorageInterface
{
    /** @var string 参数名称 */
    public string $name;

    /** @var string 编码类型 */
    public string $type;

    public SchemaStorage $items;

    /** @var string 参数说明 */
    public string $description;

    /** @var string 试例 */
    public string $example;
}
