# Configuration

Creating a `.openapi.php` file in the project root will override the default configuration.

```php
use Astral\Serialize\OpenApi\Storage\OpenAPI\ServersStorage;

return [
    /**
     * The address for OpenApi UI to get OpenApi Json.
     */
    'doc_url' => 'http://127.0.0.1:8089',

     /**
     * Title of the API documentation.
     */
    'title' => 'API Docs',

     /**
     * Description of the API documentation.
     */
    'description' => 'API Docs description.',

    /**
     * Global request headers to be added to each request.
     * Each request header should include a name, example, and description.
     *
     * Example:
     * [
     *     'name'        => 'Authorization',
     *     'example'     => 'Bearer true',
     *     'description' => 'Authentication token'
     * ]
     */
    'headers' => [],

     /**
     * Base URL (server) for the API documentation.
     * Multiple environments can be defined, such as production, testing, etc.
     *
     * @type ServersStorage[] $service
     */
    'service' => [
        new ServersStorage('http://127.0.0.1', 'Dev'),
        // Test environment, also add environment variable
        (new ServersStorage('http://test.explore.com', 'Test'))
            ->addVariable('admin_token', 'variable description', '123'),
    ],

    /**
     * Directories to be excluded from scanning.
     * These paths are relative to the project root directory.
     *
     * Default excluded directories:
     * - /vendor
     * - /tests
     * - /migrations
     * Example:
     * ['/sdk', '/app']
     *
     */
    'exclude_dirs' => [],
];
```