<?php
/**
 * Test bootstrap for EravurGo unit tests.
 *
 * - Ensures config/db.php exists (required by the page scripts under test).
 *   A throwaway connection string is enough: no real MySQL server is needed
 *   because every test that touches the DB runs in an isolated subprocess
 *   and only asserts on the validation / die() paths, which happen before
 *   any query result is inspected.
 * - Provides helpers to run page scripts as if they were HTTP requests.
 */

define('ERAVURGO_ROOT', dirname(__DIR__));
define('ERAVURGO_TESTS', __DIR__);

require_once ERAVURGO_TESTS . '/TestRunner.php';

// --- Ensure a non-committed config/db.php exists for includes -----------------
if (!is_dir(ERAVURGO_ROOT . '/config')) {
    mkdir(ERAVURGO_ROOT . '/config', 0777, true);
}
if (!file_exists(ERAVURGO_ROOT . '/config/db.php')) {
    copy(ERAVURGO_ROOT . '/config/db.example.php', ERAVURGO_ROOT . '/config/db.php');
}

/**
 * Renders a project PHP script in an isolated CLI subprocess with simulated
 * superglobals, and returns everything it printed (including die() messages).
 *
 * @param string $scriptRelativePath e.g. 'review.php' or 'admin/login.php'
 * @param array  $get    simulated $_GET
 * @param array  $post   simulated $_POST
 * @param array  $server overrides for $_SERVER
 * @param array  $session key/values to seed into $_SESSION (via session env)
 * @return string captured output
 */
function renderPage(string $scriptRelativePath, array $get = [], array $post = [], array $server = [], array $session = []): string
{
    $root = str_replace('\\', '/', ERAVURGO_ROOT);
    $bootstrap = str_replace('\\', '/', ERAVURGO_TESTS . '/page-bootstrap.php');

    $payload = json_encode([
        'root'    => $root,
        'script'  => $scriptRelativePath,
        'get'     => $get,
        'post'    => $post,
        'server'  => $server,
        'session' => $session,
    ]);

    // Payload passed via base64 to avoid quoting problems.
    $b64 = base64_encode($payload);

    $cmd = sprintf('%s %s %s 2>&1', escapeshellarg(PHP_BINARY), escapeshellarg($bootstrap), escapeshellarg($b64));
    return (string)shell_exec($cmd);
}
