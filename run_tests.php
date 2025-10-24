<?php
/**
 * PHPUnit Test Runner Script
 * For running unit tests with PHPUnit
 */

// Check if PHPUnit is available
if (!class_exists('PHPUnit\Framework\TestCase')) {
    echo "PHPUnit is not installed. Please run 'composer install' first.\n";
    exit(1);
}

echo "PHPUnit Testing Framework - ProductsController Test\n";
echo "========================================================\n\n";

// Set up autoloading if not using Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Run PHPUnit tests
if (isset($argv) && in_array('--phpunit', $argv)) {
    // This would be run via command line: php run_tests.php --phpunit
    echo "Run PHPUnit tests using: ./vendor/bin/phpunit tests/ProductsControllerTest.php\n";
    exit(0);
} else {
    echo "To run tests, install PHPUnit and run: ./vendor/bin/phpunit tests/ProductsControllerTest.php\n";
    echo "Or install with Composer: composer install\n";
}
