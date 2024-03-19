<?php
require_once './vendor/autoload.php';

use Server\Handler;

try {
    $API = new Handler();

    $result = $API->handleRequest();

    exit($result);
} catch (Exception $e) {
    // Log exception
    file_put_contents(
        __DIR__ . "/Logs/exception_log.txt",
        "[" . date('Y-m-d H:i:s') . "]\n" . $e . "\n" . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );

    header('Content-Type: application/json');

    exit(json_encode([
        'message' => 'Internal Server Error : Something went wrong.',
    ]));
}
