<?php

namespace Wilkques\Helpers\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Asserts that a variable is of type int.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     *
     * @psalm-assert int $actual
     */
    public static function assertIsInt($actual, string $message = ''): void
    {
        if (PHP_MAJOR_VERSION > 5) {
            return parent::assertIsInt($actual, $message);
        }

        assert(is_int($actual), $message);
    }

    /**
     * Asserts that a variable is of type int.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     *
     * @psalm-assert int $actual
     */
    public static function assertIsString($actual, string $message = ''): void
    {
        if (PHP_MAJOR_VERSION > 5) {
            return parent::assertIsString($actual, $message);
        }

        assert(is_int($actual), $message);
    }

    /**
     * Asserts that a variable is of type int.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     *
     * @psalm-assert int $actual
     */
    public static function assertIsArray($actual, string $message = ''): void
    {
        if (PHP_MAJOR_VERSION > 5) {
            return parent::assertIsArray($actual, $message);
        }

        assert(is_int($actual), $message);
    }
}
