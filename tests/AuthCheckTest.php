<?php
/**
 * Unit tests for admin/auth_check.php — the access-control gate used by all
 * admin pages.
 *
 * The gate is exercised through a controlled replica that loads the REAL
 * auth_check.php source (tests/auth-check-runner.php), so assertions verify
 * the actual shipped logic: redirect when not logged in, session timeout
 * after 60s of inactivity (SRS §5.3), and timestamp refresh on activity.
 */

class AuthCheckTest extends TestCase
{
    /**
     * Runs the real admin/auth_check.php inside the harness with a given
     * session state and returns decoded harness results.
     *
     * The harness receives its arguments via argv, so we invoke it directly
     * as a CLI subprocess (runSnippet's generated wrapper does not forward
     * argv).
     *
     * @param array $session initial $_SESSION contents
     * @param int|null $fakeNow if set, time() inside the gate is frozen to this value
     */
    private function runGate(array $session, ?int $fakeNow = null): array
    {
        $runner = dirname(__DIR__) . '/tests/auth-check-runner.php';
        $cmd = sprintf(
            '%s %s %s %s 2>&1',
            escapeshellarg(PHP_BINARY),
            escapeshellarg($runner),
            escapeshellarg(json_encode($session)),
            escapeshellarg($fakeNow === null ? 'null' : (string)$fakeNow)
        );
        $out = (string)shell_exec($cmd);

        $json = substr($out, strrpos($out, '{') === false ? 0 : strrpos($out, '{'));
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            throw new Exception('Harness produced no JSON. Raw output: ' . $out);
        }
        if (isset($decoded['skip'])) {
            $this->markSkipped((string)$decoded['skip']);
        }
        return $decoded;
    }

    /** Marks the current test as skipped (thrown through to the runner). */
    private function markSkipped(string $reason): void
    {
        throw new SkippedTestException($reason);
    }

    public function test_redirects_to_login_when_not_logged_in()
    {
        $r = $this->runGate([]);

        $this->assertSame('login.php', $r['location'], 'anonymous visitor must be sent to login');
        $this->assertNull($r['timeout_redirect'], 'no timeout message for plain unauthenticated access');
        // Gate must stop execution right after the redirect (header + exit).
        $this->assertFalse($r['reached_end'], 'auth_check must exit() after redirecting');
    }

    public function test_allows_logged_in_admin_and_stamps_activity()
    {
        $now = time();
        $r = $this->runGate(['admin_id' => 1]);

        $this->assertFalse($r['redirected'], 'authenticated admin must not be redirected');
        $this->assertTrue($r['reached_end'], 'gate should fall through when access is allowed');
        $this->assertNotNull($r['session']['LAST_ACTIVITY'], 'LAST_ACTIVITY must be set');
        // Updated timestamp should be ~now (within a couple of seconds).
        $this->assertTrue(abs($r['session']['LAST_ACTIVITY'] - $now) <= 2,
            'LAST_ACTIVITY should be refreshed to current time');
    }

    public function test_active_session_within_timeout_is_allowed()
    {
        $now = time();
        $r = $this->runGate([
            'admin_id'      => 1,
            'LAST_ACTIVITY' => $now - 30,   // 30s ago < 60s timeout
        ]);

        $this->assertFalse($r['redirected'], '30s idle is within the 60s window');
        $this->assertTrue($r['reached_end']);
        // Timestamp must be refreshed so the session stays alive.
        $this->assertTrue($r['session']['LAST_ACTIVITY'] >= $now,
            'LAST_ACTIVITY must be updated on each request');
    }

    public function test_expired_session_is_destroyed_and_redirected_with_timeout_msg()
    {
        $now = time();
        $r = $this->runGate([
            'admin_id'      => 1,
            'LAST_ACTIVITY' => $now - 120,  // 120s ago > 60s timeout
        ], $now);                            // freeze time() at $now

        $this->assertSame('login.php?msg=timeout', $r['location'],
            'idle admin must be bounced to login with timeout notice');
        $this->assertFalse($r['reached_end'], 'gate must exit() after timeout redirect');
        $this->assertSame([], $r['session_after_destroy'],
            'session must be unset/destroyed on timeout');
    }

    public function test_boundary_exactly_at_timeout_duration_still_valid()
    {
        $now = time();
        // Condition is strictly greater than: time() - LAST_ACTIVITY > 60.
        // Exactly 60 seconds idle must NOT expire.
        $r = $this->runGate([
            'admin_id'      => 1,
            'LAST_ACTIVITY' => $now - 60,
        ], $now);

        $this->assertFalse($r['redirected'], 'exactly 60s idle is still within the window (> comparison)');
        $this->assertTrue($r['reached_end']);
    }

    public function test_one_second_past_timeout_expires()
    {
        $now = time();
        $r = $this->runGate([
            'admin_id'      => 1,
            'LAST_ACTIVITY' => $now - 61,
        ], $now);

        $this->assertSame('login.php?msg=timeout', $r['location']);
        $this->assertFalse($r['reached_end']);
    }

    public function test_missing_last_activity_on_fresh_login_is_not_treated_as_expired()
    {
        // A session created without LAST_ACTIVITY (first request after login)
        // must pass and get stamped, not be killed as "expired".
        $r = $this->runGate(['admin_id' => 2]);

        $this->assertFalse($r['redirected']);
        $this->assertNotNull($r['session']['LAST_ACTIVITY']);
    }
}
