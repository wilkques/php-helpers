<?php

namespace Wilkques\Helpers\Tests;

use PHPUnit\Framework\TestCase;
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

    public function testRand()
    {
        $rand = Strings::rand(100);

        $this->assertEquals(100, strlen($rand));
    }

    public function testGetClassBaseName()
    {
        $this->assertEquals(Strings::getClassBaseName('\Wilkques\Helpers\Objects'), 'Objects');

        $abstract = new \Wilkques\Helpers\Objects;

        $this->assertEquals(Strings::getClassBaseName($abstract), 'Objects');
    }

    public function testUcfirst()
    {
        $this->assertEquals('Abc', Strings::ucfirst('abc'));

        $this->assertEquals('Abc', Strings::ucfirst('Abc'));
    }

    public function testStudly()
    {
        $this->assertEquals('AbcEfg', Strings::studly('abc_efg'));

        $this->assertEquals('AbcEfg', Strings::studly('abc-efg'));

        $this->assertEquals('AbcEfg', Strings::studly('abc efg'));
    }

    public function testLimit()
    {
        $this->assertEquals('abc...', Strings::limit('abc efg hij', 3));

        $this->assertEquals('abc efg hij', Strings::limit('abc efg hij', 100));

        $this->assertEquals('abc***', Strings::limit('abc efg hij', 3, '***'));

        // Limit counts display width, not character count, so a
        // full-width (CJK) character counts as 2 toward the limit.
        $this->assertEquals('这是一...', Strings::limit('这是一段中文', 6));

        $this->assertEquals('这是一', Strings::limit('这是一段中文', 6, ''));

        $this->assertEquals('The PHP', Strings::limit('The PHP framework for web artisans.', 7, ''));
    }

    public function testSlug()
    {
        $this->assertEquals('abc-efg', Strings::slug('abc efg'));

        $this->assertEquals('abc_efg', Strings::slug('abc efg', '_'));

        $this->assertEquals('abc-efg', Strings::slug('Abc Efg!!!'));
    }

    public function testMask()
    {
        $this->assertEquals('ab****gh', Strings::mask('abcdefgh', '*', 2, 4));

        $this->assertEquals('abcd****', Strings::mask('abcdefgh', '*', 4));

        $this->assertEquals('abcdef**', Strings::mask('abcdefgh', '*', -2));
    }

    public function testPadLeftRightBoth()
    {
        $this->assertEquals('__abc', Strings::padLeft('abc', 5, '_'));

        $this->assertEquals('abc__', Strings::padRight('abc', 5, '_'));

        $this->assertEquals('_abc_', Strings::padBoth('abc', 5, '_'));

        // str_pad() counts bytes, not characters — a multibyte string
        // whose byte length already exceeds the target got zero padding
        // even though its character count was well under it.
        $this->assertEquals('***你好', Strings::padLeft('你好', 5, '*'));

        $this->assertEquals('你好***', Strings::padRight('你好', 5, '*'));

        $this->assertEquals('  ❤MultiByte☆   ', Strings::padBoth('❤MultiByte☆', 16));

        $this->assertEquals('❤☆❤MultiByte☆❤☆❤', Strings::padBoth('❤MultiByte☆', 16, '❤☆'));

        $this->assertEquals('❤☆❤☆❤❤MultiByte☆', Strings::padLeft('❤MultiByte☆', 16, '❤☆'));

        $this->assertEquals('❤MultiByte☆❤☆❤☆❤', Strings::padRight('❤MultiByte☆', 16, '❤☆'));
    }

    public function testHeadline()
    {
        $this->assertEquals('Abc Efg', Strings::headline('abc_efg'));

        $this->assertEquals('Abc Efg', Strings::headline('abcEfg'));

        $this->assertEquals('Abc Efg', Strings::headline('abc-efg'));
    }

    public function testWordCount()
    {
        $this->assertEquals(2, Strings::wordCount('hello world'));
    }

    public function testIsUuid()
    {
        $this->assertTrue(Strings::isUuid('9f8f8f8f-1234-4321-abcd-1234567890ab'));

        $this->assertFalse(Strings::isUuid('not-a-uuid'));

        $this->assertFalse(Strings::isUuid(123));
    }

    public function testIsUlid()
    {
        $this->assertTrue(Strings::isUlid('01ARZ3NDEKTSV4RRFFQ69G5FAV'));

        $this->assertFalse(Strings::isUlid('not-a-ulid'));

        $this->assertFalse(Strings::isUlid(123));
    }

    public function testSquish()
    {
        $this->assertEquals('abc efg', Strings::squish('  abc   efg  '));

        // Hangul filler codepoints (U+3164/U+1160) render as blank but
        // aren't matched by \s, so they must be collapsed explicitly too.
        $this->assertEquals(
            'laravel php framework',
            Strings::squish("laravel\xE3\x85\xA4\xE3\x85\xA4\xE3\x85\xA4php\xE3\x85\xA4framework")
        );

        $this->assertEquals(
            'laravel php framework',
            Strings::squish("laravel\xE1\x85\xA0\xE1\x85\xA0\xE1\x85\xA0\xE1\x85\xA0\xE1\x85\xA0\xE1\x85\xA0\xE1\x85\xA0\xE1\x85\xA0\xE1\x85\xA0\xE1\x85\xA0php\xE1\x85\xA0\xE1\x85\xA0framework")
        );
    }

    public function testPlural()
    {
        $this->assertEquals('cars', Strings::plural('car'));

        $this->assertEquals('boxes', Strings::plural('box'));

        $this->assertEquals('cities', Strings::plural('city'));

        $this->assertEquals('leaves', Strings::plural('leaf'));

        $this->assertEquals('knives', Strings::plural('knife'));

        $this->assertEquals('children', Strings::plural('child'));

        $this->assertEquals('car', Strings::plural('car', 1));
    }

    public function testSingular()
    {
        $this->assertEquals('car', Strings::singular('cars'));

        $this->assertEquals('box', Strings::singular('boxes'));

        $this->assertEquals('city', Strings::singular('cities'));

        $this->assertEquals('leaf', Strings::singular('leaves'));

        $this->assertEquals('knife', Strings::singular('knives'));

        $this->assertEquals('child', Strings::singular('children'));
    }
}
