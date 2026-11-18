<?php
/**
 * Subprocess helper: runs a project page script with simulated superglobals.
 * Invoked by renderPage() in tests/bootstrap.php with a base64 JSON payload.
 */

error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', '1');

if (PHP_SAPI !== 'cli') {
    if (!headers_sent()) {
        http_response_code(403);
    }
    fwrite(STDERR, "FORBIDDEN\n");
    exit(1);
}

$payload = json_decode(base64_decode($argv[1] ?? ''), true);
if (!is_array($payload)) {
    fwrite(STDERR, "BAD PAYLOAD\n");
    exit(2);
}

chdir($payload['root']);

$_GET    = $payload['get'];
$_POST   = $payload['post'];
$_SERVER = array_merge([
    'REQUEST_METHOD' => empty($payload['post']) ? 'GET' : 'POST',
    'PHP_SELF'       => '/' . $payload['script'],
    'HTTP_COOKIE'    => '',
], $payload['server']);

// Seed session (used e.g. by admin/auth_check.php tests).
session_id('test' . md5(json_encode($payload)));
session_start();
foreach ($payload['session'] as $k => $v) {
    $_SESSION[$k] = $v;
}

$script = $payload['root'] . '/' . $payload['script'];
if (!file_exists($script)) {
    fwrite(STDERR, "NO SCRIPT: $script\n");
    exit(3);
}

include $script;
