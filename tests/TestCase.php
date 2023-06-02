<?php

namespace Kwaadpepper\Enum\Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Automatically loads environment file if available.
     *
     * @var boolean
     */
    protected $loadEnvironmentVariables = false;

    /**
     * Setup custom phpunit catch for deprecated error cathing.
     *
     * @return void
     */
    public function setUp(): void
    {
        set_error_handler(
            static function ($errno, $errstr) {
                throw new \Exception($errstr, $errno);
            },
            E_ALL
        );
        parent::setUp();
    }

    /**
     * Restore phpunit handler
     *
     * @return void
     */
    public function tearDown(): void
    {
        restore_error_handler();
        parent::tearDown();
    }
}
