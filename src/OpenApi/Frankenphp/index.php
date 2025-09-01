<?php

use Astral\Serialize\OpenApi;

ignore_user_abort(true);
require_once dirname(__DIR__, 6) . '/vendor/autoload.php';

$handler = static function () {
    try {
        header('Content-Type: application/json');
        echo (new OpenApi())->handleByFolders()->toString();
    } catch (ReflectionException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage(), 'code' => $e->getCode()]);
    }
};

$maxRequests = (int)($_SERVER['MAX_REQUESTS'] ?? 0);
for ($nbRequests = 0; !$maxRequests || $nbRequests < $maxRequests; ++$nbRequests) {
    $keepRunning = frankenphp_handle_request($handler);
    gc_collect_cycles();
    if (!$keepRunning) {
        break;
    }
}
