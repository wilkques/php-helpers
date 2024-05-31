<?php

namespace Wilkques\Helpers\Tests;

use PHPUnit\Framework\TestCase;
use Wilkques\Helpers\Objects;

class ObjectsTest extends TestCase
{
    public function testSet()
    {
        $array = array(1, 2, 3);

        Objects::set($array, 0, 2);

        $this->assertEquals(
            $array,
            array(
                2, 2, 3
            )
        );

        Objects::set($array, '1', 3);

        $this->assertEquals(
            $array,
            array(
                2, 3, 3
            )
        );

        Objects::set($array, 'abc', 4);

        $this->assertEquals(
            $array,
            array(
                2, 3, 3, 'abc' => 4
            )
        );
    }

    public function testGet()
    {
        $array = array(1, 2, 3, 'abc' => 4);

        $value = Objects::get($array, 0);

        $this->assertEquals($value, 1);

        $value = Objects::get($array, 'abc');

        $this->assertEquals($value, 4);

        $value = Objects::get($array, 'efg');

        $this->assertEquals($value, null);
    }

    public function testExists()
    {
        $array = array(
            'abc' => 123,
            456
        );

        $this->assertTrue(
            Objects::exists($array, 'abc')
        );

        $this->assertTrue(
            Objects::exists($array, 0)
        );

        $this->assertFalse(
            Objects::exists($array, 'efg')
        );

        $this->assertFalse(
            Objects::exists($array, 1)
        );

        // Create a mock object for the MyArray class
        $mock = $this->createMock(\ArrayAccess::class);

        // Set up expectations for offsetExists method
        $mock->method('offsetExists')
            ->with('abc')
            ->willReturn(true);

        // Set up expectations for offsetGet method
        $mock->method('offsetGet')
            ->with('abc')
            ->willReturn(123);

        // Set up expectations for offsetSet method
        $mock->method('offsetSet')
            ->with('abc', 123);

        // Set up expectations for offsetUnset method
        $mock->method('offsetUnset')
            ->with('abc');

        // Test the mock object
        $this->assertTrue(Objects::exists($mock, 'abc'));
    }

    public function testValue()
    {
        $this->assertIsArray(
            Objects::value(array())
        );

        $this->assertIsInt(
            Objects::value(1)
        );

        $this->assertIsString(
            Objects::value('')
        );

        $this->assertNull(
            Objects::value(null)
        );

        $this->assertNull(
            Objects::value(function () {
            })
        );

        $this->assertIsArray(
            Objects::value(function () {
                return array();
            })
        );

        $this->assertIsInt(
            Objects::value(function () {
                return 123;
            })
        );

        $this->assertIsString(
            Objects::value(function () {
                return '';
            })
        );

        $this->assertIsArray(
            Objects::value(function ($array) {
                return $array;
            }, array())
        );

        $this->assertIsInt(
            Objects::value(function ($int) {
                return $int;
            }, 123)
        );

        $this->assertIsString(
            Objects::value(function ($string) {
                return $string;
            }, '')
        );
    }
}
