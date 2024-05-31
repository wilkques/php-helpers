<?php

namespace Wilkques\Helpers\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

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
    final public static function assertIsInt($actual, string $message = ''): void
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
    final public static function assertIsString($actual, string $message = ''): void
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
    final public static function assertIsArray($actual, string $message = ''): void
    {
        if (PHP_MAJOR_VERSION >= 7) {
            parent::assertIsArray($actual, $message);

            return;
        }

        parent::assertTrue(is_array($actual), $message);
    }
}
