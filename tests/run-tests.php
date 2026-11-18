<?php
/**
 * Entry point for the EravurGo unit test suite.
 *
 * Run from anywhere:  php tests/run-tests.php
 */

require_once __DIR__ . '/bootstrap.php';

$testFiles = glob(__DIR__ . '/*Test.php');
sort($testFiles);

$runner = new TestRunner();
exit($runner->run($testFiles));
