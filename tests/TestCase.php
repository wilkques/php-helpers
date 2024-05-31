<?php

namespace Wilkques\Helpers\Tests;

use Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
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
        if (PHP_MAJOR_VERSION >= 7) {
            parent::assertIsInt($actual, $message);

            return;
        }

        parent::assertTrue(is_int($actual), $message);
    }

    /**
     * Asserts that a variable is of type string.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     *
     * @psalm-assert string $actual
     */
    public static function assertIsString($actual, string $message = ''): void
    {
        if (PHP_MAJOR_VERSION >= 7) {
            parent::assertIsString($actual, $message);

            return;
        }

        parent::assertTrue(is_string($actual), $message);
    }
    
    /**
     * Asserts that a variable is of type array.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     *
     * @psalm-assert array $actual
     */
    public static function assertIsArray($actual, string $message = ''): void
    {
        if (PHP_MAJOR_VERSION >= 7) {
            parent::assertIsArray($actual, $message);

            return;
        }

        parent::assertTrue(is_array($actual), $message);
    }
}
