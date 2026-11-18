<?php
/**
 * Minimal zero-dependency unit test runner for EravurGo.
 *
 * Usage: php tests/run-tests.php
 *
 * Test classes extend TestCase and define public methods starting with "test_".
 * Each test method runs on a fresh instance, so state cannot leak between tests.
 *
 * Code under test that calls die()/exit() (e.g. the page scripts) is exercised
 * through runSnippet(), which executes the code in an isolated child process
 * and captures its output — so fatal paths are testable without killing the
 * runner itself.
 */

abstract class TestCase
{
    /** @var int */
    public $assertionCount = 0;

    protected function markSkipped(string $reason): void
    {
        throw new SkippedTestException($reason);
    }

    protected function assertTrue($condition, string $message = ''): void
    {
        $this->assertionCount++;
        if ($condition !== true) {
            throw new Exception($message ?: 'Failed asserting that value is true.');
        }
    }

    protected function assertFalse($condition, string $message = ''): void
    {
        $this->assertionCount++;
        if ($condition !== false) {
            throw new Exception($message ?: 'Failed asserting that value is false.');
        }
    }

    protected function assertEquals($expected, $actual, string $message = ''): void
    {
        $this->assertionCount++;
        if ($expected != $actual) {
            throw new Exception(
                ($message ?: 'Values not equal') .
                ' (expected: ' . var_export($expected, true) .
                ', actual: ' . var_export($actual, true) . ')'
            );
        }
    }

    protected function assertSame($expected, $actual, string $message = ''): void
    {
        $this->assertionCount++;
        if ($expected !== $actual) {
            throw new Exception(
                ($message ?: 'Values not identical') .
                ' (expected: ' . var_export($expected, true) .
                ', actual: ' . var_export($actual, true) . ')'
            );
        }
    }

    protected function assertNull($value, string $message = ''): void
    {
        $this->assertionCount++;
        if ($value !== null) {
            throw new Exception($message ?: 'Failed asserting that value is null.');
        }
    }

    protected function assertNotNull($value, string $message = ''): void
    {
        $this->assertionCount++;
        if ($value === null) {
            throw new Exception($message ?: 'Failed asserting that value is not null.');
        }
    }

    protected function assertContainsString(string $needle, string $haystack, string $message = ''): void
    {
        $this->assertionCount++;
        if (strpos($haystack, $needle) === false) {
            throw new Exception(
                ($message ?: 'Substring not found') .
                " (needle: '$needle' missing from '" . substr($haystack, 0, 300) . "')"
            );
        }
    }

    protected function assertNotContainsString(string $needle, string $haystack, string $message = ''): void
    {
        $this->assertionCount++;
        if (strpos($haystack, $needle) !== false) {
            throw new Exception($message ?: "Unexpectedly found '$needle' in output.");
        }
    }

    protected function expectException(string $class, callable $fn, string $message = ''): void
    {
        $this->assertionCount++;
        try {
            $fn();
        } catch (\Throwable $e) {
            if (!($e instanceof $class)) {
                throw new Exception(($message ?: 'Wrong exception type') . ': got ' . get_class($e));
            }
            return;
        }
        throw new Exception($message ?: "Expected exception '$class' was not thrown.");
    }

    /**
     * Executes a PHP snippet in an isolated child CLI process and returns
     * everything it printed (including die()/exit() messages).
     */
    protected function runSnippet(string $code): string
    {
        $this->assertionCount++;
        $tmpFile = tempnam(sys_get_temp_dir(), 'eravurgo_snippet_') . '.php';
        file_put_contents($tmpFile, "<?php\n" . $code);
        $output = shell_exec(PHP_BINARY . ' ' . escapeshellarg($tmpFile) . ' 2>&1');
        unlink($tmpFile);
        return (string)$output;
    }
}

final class TestRunner
{
    /** @var string[] */
    private $errors = [];
    private $passed = 0;
    private $failed = 0;
    private $assertions = 0;

    public function run(array $testFiles): int
    {
        foreach ($testFiles as $file) {
            require_once $file;
        }

        foreach (get_declared_classes() as $class) {
            if ((new ReflectionClass($class))->isAbstract()) {
                continue;
            }
            if (is_subclass_of($class, 'TestCase')) {
                $this->runClass($class);
            }
        }

        return $this->summary();
    }

    private function runClass(string $class): void
    {
        echo "\n== $class ==\n";
        $reflection = new ReflectionClass($class);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (strpos($method->getName(), 'test_') !== 0) {
                continue;
            }

            $label = $class . '::' . $method->getName();
            $instance = new $class();

            try {
                $instance->{$method->getName()}();
                $this->passed++;
                $this->assertions += $instance->assertionCount;
                echo "  [PASS] {$method->getName()} ({$instance->assertionCount} assertions)\n";
            } catch (\Throwable $e) {
                $this->failed++;
                $this->assertions += $instance->assertionCount;
                $this->errors[] = "$label -> " . $e->getMessage();
                echo "  [FAIL] {$method->getName()} : " . $e->getMessage() . "\n";
            }
        }
    }

    private function summary(): int
    {
        echo "\n----------------------------------------\n";
        echo 'Tests: ' . ($this->passed + $this->failed) .
             ", Passed: {$this->passed}, Failed: {$this->failed}" .
             ", Assertions: {$this->assertions}\n";
        if ($this->failed > 0) {
            echo "\nFailures:\n";
            foreach ($this->errors as $i => $err) {
                echo '  ' . ($i + 1) . ") $err\n";
            }
            return 1;
        }
        echo "OK\n";
        return 0;
    }
}
