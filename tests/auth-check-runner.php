<?php
/**
 * Harness for AuthCheckTest: executes the REAL admin/auth_check.php file in a
 * controlled environment.
 *
 * Technique:
 *  - The gate's source is loaded and its PHP tags are stripped so it runs as
 *    plain code inside this harness (no stray output, no session_start()).
 *  - Built-in calls made by the gate — header(), time(), session_unset(),
 *    session_destroy(), exit() — are intercepted with uopz test doubles, so we
 *    can observe redirects, frozen clocks and session mutation without a web
 *    server. Requires the pecl uopz extension; prints SKIP if missing.
 *
 * Args: argv[1] = JSON of initial $_SESSION, argv[2] = "null" or a frozen time.
 * Prints a JSON report on stdout.
 */

if (!function_exists('uopz_set_mock')) {
    echo json_encode(['skip' => 'uopz extension required for auth_check harness']);
    exit(0);
}

class ExitSignal extends \Exception {}

// --- Test doubles for the built-ins the gate calls ---------------------------
uopz_set_mock('header', function (string $h) {
    $GLOBALS['ug_headers'][] = $h;
});

uopz_set_mock('time', function (): int {
    return $GLOBALS['ug_fake_time'] ?? time();
});

uopz_set_mock('session_unset', function (): void {
    $GLOBALS['ug_session_unset_called'] = true;
    $_SESSION = [];
});

uopz_set_mock('session_destroy', function (): void {
    $GLOBALS['ug_session_destroy_called'] = true;
});

// exit()/die() inside the gate must not kill the harness -> convert to throw.
foreach (['exit', 'die'] as $kw) {
    uopz_set_mock($kw, function ($status = 0) {
        throw new ExitSignal('exit called with status ' . $status);
    });
}

// --- Setup --------------------------------------------------------------------
$initialSession = json_decode($argv[1] ?? '[]', true);
$frozen         = (($argv[2] ?? 'null') !== 'null') ? (int)$argv[2] : null;

$GLOBALS['ug_headers']                = [];
$GLOBALS['ug_fake_time']              = $frozen;
$GLOBALS['ug_session_unset_called']   = false;
$GLOBALS['ug_session_destroy_called'] = false;

$_SESSION = $initialSession;

// Load the real gate, strip PHP tags so it becomes executable code.
$gatePath = dirname(__DIR__) . '/admin/auth_check.php';
$gateCode = file_get_contents($gatePath);
if ($gateCode === false) {
    fwrite(STDERR, "Cannot read $gatePath\n");
    exit(2);
}
$gateCode = preg_replace('/^\s*<\?php\s*/', '', $gateCode);
$gateCode = preg_replace('/\?>\s*$/', '', $gateCode);

$tmp = tempnam(sys_get_temp_dir(), 'ug_gate_') . '.php';
file_put_contents($tmp, "<?php\n" . $gateCode);

$reachedEnd = false;
try {
    include $tmp; // gate runs here; unqualified built-in calls hit our mocks
    $reachedEnd = true;
} catch (ExitSignal $e) {
    // Gate called exit() — expected on redirect paths.
} finally {
    @unlink($tmp);
}

$location = null;
foreach ($GLOBALS['ug_headers'] as $h) {
    if (stripos($h, 'Location:') === 0) {
        $location = trim(substr($h, strlen('Location:')));
    }
}

echo json_encode([
    'redirected'             => count($GLOBALS['ug_headers']) > 0,
    'location'               => $location,
    'timeout_redirect'       => ($location === 'login.php?msg=timeout') ? $location : null,
    'headers'                => $GLOBALS['ug_headers'],
    'reached_end'            => $reachedEnd,
    'session'                => $_SESSION,
    'session_after_destroy'  => $GLOBALS['ug_session_destroy_called'] ? $_SESSION : null,
    'session_unset_called'   => $GLOBALS['ug_session_unset_called'],
    'session_destroy_called' => $GLOBALS['ug_session_destroy_called'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
