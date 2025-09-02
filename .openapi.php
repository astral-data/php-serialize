<?php

use Astral\Serialize\OpenApi\Storage\OpenAPI\ServersStorage;

return [

    'doc_url' => 'http://127.0.0.1:8089',

    // API Document Title
    'title' => 'API Docs',

    // Description of the API document
    'description' => 'API Docs description.',

    /**
     * Global headers to be added to every request.
     * Each header should include name, example, and description.
     *
     * Example:
     * [
     *     'name'        => 'Authorization',
     *     'example'     => 'Bearer token',
     *     'description' => 'Authentication token'
     * ]
     */
    'headers' => [],

    /**
     * Service base URLs (servers).
     * You can define multiple environments like production, staging, etc.
     *
     * @type ServersStorage[] $service
     */
    'service' => [
        new ServersStorage('http://127.0.0.1', 'Default'),
    ],

    /**
     * Directories to exclude from scanning.
     * These paths are relative to the project root directory.
     *
     * Default excluded directories:
     * - /vendor
     * - /tests
     * - /migrations
     * Example:
     * ['/sdk', '/app']
     *
     * You can override or extend this list in your config file.
     */
    'exclude_dirs' => [],

    /**
     * Response Data Structure Definition
     *
     * Defines the basic structure of API responses, including status code, return message, and data body
     *
     */
    'response' => [
        'code' => ['description' =>'code', 'example'=> 200],
        'message' => ['description' =>'message', 'example'=> 'success'],
        'data' => 'T',
    ]
];
