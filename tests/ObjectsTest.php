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

    public function testGetTraversesArrayAccess()
    {
        $inner = new ObjectsTestArrayAccessFixture(array('name' => 'John'));

        $outer = new ObjectsTestArrayAccessFixture(array('price' => 56, 'user' => $inner, 'email' => null));

        $this->assertEquals(56, Objects::get($outer, 'price'));

        $this->assertEquals('John', Objects::get($outer, 'user.name'));

        $this->assertEquals('void', Objects::get($outer, 'foo', 'void'));

        $this->assertEquals('void', Objects::get($outer, 'user.foo', 'void'));

        $this->assertNull(Objects::get($outer, 'email', 'Not found'));
    }

    public function testGetWithNestedWildcards()
    {
        $array = array(
            'users' => array(
                array('first' => 'taylor', 'email' => 'taylor@example.com'),
                array('first' => 'abigail'),
                array('first' => 'dayle'),
            ),
        );

        $this->assertEquals(array('taylor', 'abigail', 'dayle'), Objects::get($array, 'users.*.first'));

        $this->assertEquals(array('taylor@example.com', null, null), Objects::get($array, 'users.*.email', 'irrelevant'));
    }

    public function testGetWithDoubleNestedWildcardCollapsesResult()
    {
        $array = array(
            'posts' => array(
                array(
                    'comments' => array(
                        array('author' => 'taylor', 'likes' => 4),
                        array('author' => 'abigail', 'likes' => 3),
                    ),
                ),
                array(
                    'comments' => array(
                        array('author' => 'dayle'),
                    ),
                ),
            ),
        );

        $this->assertEquals(
            array('taylor', 'abigail', 'dayle'),
            Objects::get($array, 'posts.*.comments.*.author')
        );

        $this->assertEquals(
            array(4, 3, null),
            Objects::get($array, 'posts.*.comments.*.likes')
        );
    }

    public function testGetFirstLastDirectives()
    {
        $array = array('one', 'two', 'three');

        $this->assertEquals('one', Objects::get($array, '{first}'));

        $this->assertEquals('three', Objects::get($array, '{last}'));
    }

    public function testGetFirstLastDirectivesOnKeyedArraysFollowInsertionOrder()
    {
        $array = array('a' => 1, 'z' => 2, 'm' => 3);

        $this->assertEquals(1, Objects::get($array, '{first}'));

        $this->assertEquals(3, Objects::get($array, '{last}'));
    }

    public function testGetFirstLastDirectivesOnArrayAccessIterable()
    {
        $fixture = new ObjectsTestArrayAccessIterableFixture(array('one', 'two', 'three'));

        $this->assertEquals('one', Objects::get($fixture, '{first}'));

        $this->assertEquals('three', Objects::get($fixture, '{last}'));
    }

    public function testGetEscapedSegmentKeys()
    {
        $array = array(
            'symbols' => array(
                '{last}' => array('description' => 'dollar'),
                '*' => array('description' => 'asterisk'),
                '{first}' => array('description' => 'caret'),
            ),
        );

        $this->assertEquals('caret', Objects::get($array, 'symbols.\{first}.description'));

        $this->assertEquals('asterisk', Objects::get($array, 'symbols.\*.description'));

        $this->assertEquals('dollar', Objects::get($array, 'symbols.\{last}.description'));

        $this->assertEquals(
            array('dollar', 'asterisk', 'caret'),
            Objects::get($array, 'symbols.*.description')
        );
    }

    public function testGetFirstLastDirectiveInsertionOrderEdgeCase()
    {
        // Insertion order is {last}, *, {first} — the UNESCAPED {first}
        // directive must resolve to the first-INSERTED key ('{last}'),
        // and the unescaped {last} directive to the last-inserted key
        // ('{first}'), regardless of what those keys are literally named.
        $array = array(
            'symbols' => array(
                '{last}' => array('description' => 'dollar'),
                '*' => array('description' => 'asterisk'),
                '{first}' => array('description' => 'caret'),
            ),
        );

        $this->assertEquals('dollar', Objects::get($array, 'symbols.{first}.description'));

        $this->assertEquals('caret', Objects::get($array, 'symbols.{last}.description'));
    }

    public function testGetFirstLastDirectiveOnEmptyArrayReturnsDefault()
    {
        $this->assertEquals('default', Objects::get(array(), '{first}', 'default'));

        $this->assertEquals('default', Objects::get(array(), '{last}', 'default'));
    }

    public function testGetFirstLastDirectiveNested()
    {
        $array = array(
            'flights' => array(
                array('segments' => array(
                    array('from' => 'LHR'),
                    array('from' => 'IST'),
                )),
            ),
        );

        $this->assertEquals('LHR', Objects::get($array, 'flights.0.segments.{first}.from'));

        $this->assertEquals('IST', Objects::get($array, 'flights.0.segments.{last}.from'));
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

        $createMock = method_exists($this, 'createMock') ? 'createMock' : 'getMock';

        // Create a mock object for the MyArray class
        $mock = call_user_func(array($this, $createMock), '\ArrayAccess');

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

        // float keys are cast to string before the array_key_exists()
        // lookup, matching how PHP itself casts a float array key to int
        // (via string) when it's actually stored as a key.
        $this->assertTrue(Objects::exists(array('1.5' => 'x'), 1.5));
    }

    public function testValue()
    {
        $this->assertThat(Objects::value(array()), $this->isType('array'));

        $this->assertThat(Objects::value(1), $this->isType('int'));

        $this->assertThat(Objects::value(''), $this->isType('string'));

        $this->assertNull(
            Objects::value(null)
        );

        $this->assertNull(
            Objects::value(function () {
            })
        );

        $this->assertThat(Objects::value(function () {
            return array();
        }), $this->isType('array'));

        $this->assertThat(Objects::value(function () {
            return 123;
        }), $this->isType('int'));

        $this->assertThat(Objects::value(function () {
            return '';
        }), $this->isType('string'));
        
        $this->assertThat(Objects::value(function ($array) {
            return $array;
        }, array()), $this->isType('array'));

        $this->assertThat(Objects::value(function ($int) {
            return $int;
        }, 123), $this->isType('int'));

        $this->assertThat(Objects::value(function ($string) {
            return $string;
        }, ''), $this->isType('string'));
    }
}

/**
 * Minimal real (not mocked) ArrayAccess implementation — Objects::get()
 * needs to isset()/offsetExists()/offsetGet() through it recursively,
 * which the method()/willReturn() mock builder used elsewhere in this
 * file can't express.
 */
class ObjectsTestArrayAccessFixture implements \ArrayAccess
{
    protected $items;

    public function __construct($items = array())
    {
        $this->items = $items;
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]) || array_key_exists($offset, $this->items);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}

/**
 * ArrayAccess + IteratorAggregate fixture — exercises Objects::get()'s
 * normalizeForKeyLookup() Traversable branch for {first}/{last}.
 */
class ObjectsTestArrayAccessIterableFixture implements \ArrayAccess, \IteratorAggregate
{
    protected $items;

    public function __construct($items = array())
    {
        $this->items = $items;
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}
