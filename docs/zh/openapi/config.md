# 配置

在项目根目录创建 `.openapi.php` 文件会覆盖默认配置

```php
use Astral\Serialize\OpenApi\Storage\OpenAPI\ServersStorage;

return [
    /**
     * OpenApi UI 获取OpenApi Json 的地址
     */
    'doc_url' => 'http://127.0.0.1:8089',

     /**
     * API 文档的标题。
     */
    'title' => 'API Docs',

     /**
     * API 文档的描述。
     */
    'description' => 'API Docs description.',

    /**
     * 每个请求中需要添加的全局请求头。
     * 每个请求头应包含名称、示例和描述。
     *
     * 示例：
     * [
     *     'name'        => 'Authorization',
     *     'example'     => 'Bearer true',
     *     'description' => '认证令牌'
     * ]
     */
    'headers' => [],

     /**
     * 服务的基础 URL（服务器）。
     * 可以定义多个环境，例如生产环境、测试环境等。
     *
     * @type ServersStorage[] $service
     */
    'service' => [
        new ServersStorage('http://127.0.0.1', 'Dev'),
        // 测试环境 同时增加环境变量
        (new ServersStorage('http://test.explore.com', 'Test'))
            ->addVariable('admin_token', '变量说明', '123'),
    ],

    /**
     * 需要排除的扫描目录。
     * 这些路径是相对于项目根目录的。
     *
     * 默认排除的目录：
     * - /vendor
     * - /tests
     * - /migrations
     * 示例：
     * ['/sdk', '/app']
     *
     */
    'exclude_dirs' => [],
    
    /**
     * 响应数据结构定义
     * 
     * 定义API响应的基本结构，包含状态码、返回信息和数据主体
     *
     */
    'response' => [
        'code' => ['description' =>'状态码', 'example'=> 200],
        'message' => ['description' =>'返回信息', 'example'=> 'success'],
        'data' => 'T',
    ]
];
```