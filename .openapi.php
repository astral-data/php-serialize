<?php

use Astral\Serialize\OpenApi\Storage\OpenAPI\ServersStorage;

return [
    'title' => 'API Docs',

    'description' => 'API Docs description.',

    /**
     * 向全局头部参数存储中添加一个的头部参数。
     * @param string $name
     * @param string $example
     * @param string $description
     */
    'headers' => [

    ],

    'service' => [
        new ServersStorage('http://127.0.0.1','默认环境'),
    ],
];
