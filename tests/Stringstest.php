<?php

namespace Wilkques\Helpers\Tests;

use Wilkques\Helpers\Strings;

class StringsTest extends TestCase
{
    public function testContains()
    {
        $this->assertTrue(
            Strings::contains('hello world!', 'hello')
        );

        $this->assertTrue(
            Strings::contains('hello world!', ' ')
        );

        $this->assertTrue(
            Strings::contains('hello world!', '!')
        );

        $this->assertTrue(
            Strings::contains('hello world!', 'world!')
        );

        $this->assertTrue(
            Strings::contains('hello world!', ' world')
        );

        $this->assertTrue(
            Strings::contains('hello world!', ' world!')
        );

        $this->assertTrue(
            Strings::contains('hello world!', 'w')
        );
    }

    public function testSnake()
    {
        $this->assertEquals(
            Strings::snake('abcEfg'),
            'abc_efg'
        );

        $this->assertEquals(
            Strings::snake('abcEfgHij'),
            'abc_efg_hij'
        );
    }

    public function testKebab()
    {
        $this->assertEquals(
            Strings::kebab('abcEfg'),
            'abc-efg'
        );

        $this->assertEquals(
            Strings::kebab('abcEfgHij'),
            'abc-efg-hij'
        );
    }

    public function testCamel()
    {
        $this->assertEquals(
            Strings::camel('abc_efg'),
            'abcEfg'
        );

        $this->assertEquals(
            Strings::camel('abc_efg_hij'),
            'abcEfgHij'
        );
        
        $this->assertEquals(
            Strings::camel('abc-efg'),
            'abcEfg'
        );

        $this->assertEquals(
            Strings::camel('abc-efg-hij'),
            'abcEfgHij'
        );
    }

    public function testLower()
    {
        $this->assertEquals(
            Strings::lower('abc'),
            'abc'
        );

        $this->assertEquals(
            Strings::lower('ABC'),
            'abc'
        );

        $this->assertEquals(
            Strings::lower('AbC'),
            'abc'
        );

        $this->assertEquals(
            Strings::lower('abC'),
            'abc'
        );
        
        $this->assertEquals(
            Strings::lower('abc_efg'),
            'abc_efg'
        );
        
        $this->assertEquals(
            Strings::lower('ABC_EFG'),
            'abc_efg'
        );

        $this->assertEquals(
            Strings::lower('abc_efg_hij'),
            'abc_efg_hij'
        );

        $this->assertEquals(
            Strings::lower('ABC_EFG_HIJ'),
            'abc_efg_hij'
        );
        
        $this->assertEquals(
            Strings::lower('abc-efg-hij'),
            'abc-efg-hij'
        );

        $this->assertEquals(
            Strings::lower('ABC-EFG-HIJ'),
            'abc-efg-hij'
        );
    }

    public function testUpper()
    {
        $this->assertEquals(
            Strings::upper('ABC'),
            'ABC'
        );

        $this->assertEquals(
            Strings::upper('abc'),
            'ABC'
        );

        $this->assertEquals(
            Strings::upper('AbC'),
            'ABC'
        );

        $this->assertEquals(
            Strings::upper('abC'),
            'ABC'
        );
        
        $this->assertEquals(
            Strings::upper('abc_efg'),
            'ABC_EFG'
        );
        
        $this->assertEquals(
            Strings::upper('ABC_EFG'),
            'ABC_EFG'
        );

        $this->assertEquals(
            Strings::upper('abc_efg_hij'),
            'ABC_EFG_HIJ'
        );

        $this->assertEquals(
            Strings::upper('ABC_EFG_HIJ'),
            'ABC_EFG_HIJ'
        );
        
        $this->assertEquals(
            Strings::upper('abc-efg-hij'),
            'ABC-EFG-HIJ'
        );

        $this->assertEquals(
            Strings::upper('ABC-EFG-HIJ'),
            'ABC-EFG-HIJ'
        );
    }

    public function testStartsWith()
    {
        $this->assertTrue(
            Strings::startsWith('Hello World!', 'Hello')
        );
        
        $this->assertTrue(
            Strings::startsWith('Hello World!', 'H')
        );
        
        $this->assertFalse(
            Strings::startsWith('Hello World!', 'e')
        );
        
        $this->assertFalse(
            Strings::startsWith('Hello World!', 'World')
        );
    }

    public function testEndsWith()
    {
        $this->assertTrue(
            Strings::endsWith('Hello World!', 'World!')
        );
        
        $this->assertTrue(
            Strings::endsWith('Hello World!', '!')
        );
        
        $this->assertFalse(
            Strings::endsWith('Hello World!', 'H')
        );
        
        $this->assertFalse(
            Strings::endsWith('Hello World!', 'World')
        );
    }

    public function testDelimiterReplace()
    {
        $this->assertEquals(
            Strings::delimiterReplace('abcEfg', '_'),
            'abc_efg'
        );

        $this->assertEquals(
            Strings::delimiterReplace('abcEfgHij', '_'),
            'abc_efg_hij'
        );
        
        $this->assertEquals(
            Strings::delimiterReplace('abcEfg', '-'),
            'abc-efg'
        );

        $this->assertEquals(
            Strings::delimiterReplace('abcEfgHij', '-'),
            'abc-efg-hij'
        );
    }

    public function testConvertCase()
    {
        $this->assertEquals(
            Strings::convertCase('abc'),
            'abc'
        );
        
        $this->assertEquals(
            Strings::convertCase('abc', MB_CASE_UPPER),
            'ABC'
        );
        
        $this->assertEquals(
            Strings::convertCase('ABC'),
            'abc'
        );
        
        $this->assertEquals(
            Strings::convertCase('ABC', MB_CASE_UPPER),
            'ABC'
        );

        $this->assertEquals(
            Strings::convertCase('abc_efg'),
            'abc_efg'
        );

        $this->assertEquals(
            Strings::convertCase('abc_efg', MB_CASE_UPPER),
            'ABC_EFG'
        );

        $this->assertEquals(
            Strings::convertCase('abc_efg_hij'),
            'abc_efg_hij'
        );

        $this->assertEquals(
            Strings::convertCase('abc_efg_hij', MB_CASE_UPPER),
            'ABC_EFG_HIJ'
        );

        $this->assertEquals(
            Strings::convertCase('abc-efg'),
            'abc-efg'
        );

        $this->assertEquals(
            Strings::convertCase('abc-efg', MB_CASE_UPPER),
            'ABC-EFG'
        );

        $this->assertEquals(
            Strings::convertCase('abc-efg-hij'),
            'abc-efg-hij'
        );

        $this->assertEquals(
            Strings::convertCase('abc-efg-hij', MB_CASE_UPPER),
            'ABC-EFG-HIJ'
        );
    }

    public function testKebabCaseToCamel()
    {
        $this->assertEquals(
            Strings::kebabCaseToCamel('abc-efg'),
            'abcEfg'
        );
    }

    public function testSnakeToCamel()
    {
        $this->assertEquals(
            Strings::snakeToCamel('abc_efg'),
            'abcEfg'
        );
    }
}
