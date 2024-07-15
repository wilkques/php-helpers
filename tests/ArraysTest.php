<?php

namespace Wilkques\Helpers\Tests;

use PHPUnit\Framework\TestCase;
use Wilkques\Helpers\Arrays;

class ArraysTest extends TestCase
{
    public function testOnly()
    {
        $array = Arrays::only(
            array(
                'abc' => 123,
                'efg' => 456
            ),
            'abc'
        );

        $this->assertArrayHasKey('abc', $array);

        $this->assertEquals(
            $array,
            array(
                'abc' => 123,
            )
        );

        $array = Arrays::only(
            array(
                'abc' => 123,
                'efg' => 456
            ),
            array(
                'abc',
            )
        );

        $this->assertArrayHasKey('abc', $array);

        $this->assertEquals(
            $array,
            array(
                'abc' => 123,
            )
        );
    }

    public function testKeySanke()
    {
        $array = Arrays::keySnake(
            array(
                'abcEfg' => 123,
                'hijKlm' => 456
            )
        );

        $this->assertArrayHasKey('abc_efg', $array);

        $this->assertArrayHasKey('hij_klm', $array);

        $this->assertEquals(
            $array,
            array(
                'abc_efg' => 123,
                'hij_klm' => 456,
            )
        );
    }

    public function testMap()
    {
        $self = $this;

        $array = Arrays::map(
            array(
                'abcEfg' => 123,
                'hijKlm' => 456
            ),
            function ($item, $index) use ($self) {
                $self->assertTrue(in_array($index, array('abcEfg', 'hijKlm')));

                $self->assertTrue(in_array($item, array('123', '456')));

                $self->assertTrue(in_array($item, array(123, 456)));

                return array($item, $index);
            }
        );

        $this->assertEquals(
            $array,
            array(
                array(
                    123,
                    'abcEfg',
                ),
                array(
                    456,
                    'hijKlm',
                ),
            )
        );
    }

    public function testPluck()
    {
        $default = array(
            array(
                'abcEfg' => 'hijKlm',
                'nopQrs' => 'tuvWxy',
            ),
            array(
                'abcEfg' => 'zabCde',
                'nopQrs' => 'fghIjk',
            ),
        );

        $array = Arrays::pluck(
            $default,
            'abcEfg'
        );

        $this->assertEquals(
            $array,
            array(
                'hijKlm', 'zabCde'
            )
        );

        $array = Arrays::pluck(
            $default,
            'abcEfg',
            'nopQrs',
            'upper'
        );

        $this->assertEquals(
            $array,
            array(
                'TUVWXY' => 'hijKlm',
                'FGHIJK' => 'zabCde',
            )
        );

        $array = Arrays::pluck(
            $default,
            'abcEfg',
            'nopQrs',
            'lower'
        );

        $this->assertEquals(
            $array,
            array(
                'tuvwxy' => 'hijKlm',
                'fghijk' => 'zabCde',
            )
        );

        $array = Arrays::pluck(
            $default,
            'abcEfg',
            'nopQrs',
            'snake'
        );

        $this->assertEquals(
            $array,
            array(
                'tuv_wxy' => 'hijKlm',
                'fgh_ijk' => 'zabCde',
            )
        );

        $array = Arrays::pluck(
            $default,
            'abcEfg',
            'nopQrs',
            'kebab'
        );

        $this->assertEquals(
            $array,
            array(
                'tuv-wxy' => 'hijKlm',
                'fgh-ijk' => 'zabCde',
            )
        );

        $array = Arrays::pluck(
            $default,
            'abcEfg',
            'nopQrs',
            'camel'
        );

        $this->assertEquals(
            $array,
            array(
                'tuvWxy' => 'hijKlm',
                'fghIjk' => 'zabCde',
            )
        );

        $default = array(
            array(
                'abcEfg' => array(
                    'hijKlm' => 'fghijk',
                ),
                'nopQrs' => array(
                    'tuvWxy' => 'lmnopq',
                ),
            ),
            array(
                'abcEfg' => array(
                    'hijKlm' => 'zabCde',
                ),
                'nopQrs' => array(
                    'tuvWxy' => 'rstuvw',
                ),
            ),
        );

        $array = Arrays::pluck(
            $default,
            'abcEfg.hijKlm'
        );

        $this->assertEquals(
            $array,
            array(
                'fghijk', 'zabCde'
            )
        );

        $array = Arrays::pluck(
            $default,
            'abcEfg.hijKlm',
            'nopQrs.tuvWxy'
        );

        $this->assertEquals(
            $array,
            array(
                'lmnopq' => 'fghijk',
                'rstuvw' => 'zabCde'
            )
        );

        $array = Arrays::pluck(
            $default,
            'nopQrs.tuvWxy'
        );

        $this->assertEquals(
            $array,
            array(
                'lmnopq', 'rstuvw'
            )
        );

        $array = Arrays::pluck(
            $default,
            'nopQrs.tuvWxy',
            'abcEfg.hijKlm'
        );

        $this->assertEquals(
            $array,
            array(
                'fghijk' => 'lmnopq',
                'zabCde' => 'rstuvw'
            )
        );
    }

    public function testMapWithKeys()
    {
        $array = Arrays::mapWithKeys(
            array(
                array(
                    'abcEfg' => 'hijKlm',
                    'nopQrs' => 'tuvWxy',
                ),
                array(
                    'abcEfg' => 'zabCde',
                    'nopQrs' => 'fghIjk',
                ),
            ),
            function ($item, $index) {
                return array($item['abcEfg'] => $item['nopQrs']);
            }
        );

        $this->assertEquals(
            $array,
            array(
                'hijKlm' => 'tuvWxy',
                'zabCde' => 'fghIjk',
            )
        );
    }

    public function testWhere()
    {
        $self = $this;

        $array = Arrays::where(
            array(
                123, null, '', 0, 456, 'abc' => null, 'efg' => '', 'hij' => 0
            ),
            function ($item, $index) use ($self) {
                $self->assertTrue(in_array($index, array(0, 1, 2, 3, 4, 'abc', 'efg', 'hij')));

                $self->assertTrue(in_array($item, array(123, null, '', 0, 456)));

                $self->assertTrue(in_array($item, array('123', null, '', '0', '456')));

                return $item;
            }
        );

        $this->assertEquals(
            $array,
            array(
                0 => 123,
                4 => 456,
            )
        );
    }

    public function testExcept()
    {
        $array = Arrays::except(
            array(
                'abc' => 123,
                'efg' => 456
            ),
            'abc'
        );

        $this->assertArrayHasKey('efg', $array);

        $this->assertEquals(
            $array,
            array(
                'efg' => 456
            )
        );

        $array = Arrays::except(
            array(
                'abc' => 123,
                'efg' => 456
            ),
            array(
                'abc',
            )
        );

        $this->assertArrayHasKey('efg', $array);

        $this->assertEquals(
            $array,
            array(
                'efg' => 456
            )
        );
    }

    public function testKeyField()
    {
        $array = Arrays::keyFields(
            array(
                'efg' => 456,
                'abc' => 123,
            ),
            array(
                'abc', 'efg'
            )
        );

        $this->assertEquals(
            array_keys($array),
            array(
                'abc', 'efg'
            )
        );
    }

    public function testSet()
    {
        $array = array(1, 2, 3);

        Arrays::set($array, 0, 2);

        $this->assertEquals(
            $array,
            array(
                2, 2, 3
            )
        );

        Arrays::set($array, '1', 3);

        $this->assertEquals(
            $array,
            array(
                2, 3, 3
            )
        );

        Arrays::set($array, 'abc', 4);

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

        $value = Arrays::get($array, 0);

        $this->assertEquals($value, 1);

        $value = Arrays::get($array, 'abc');

        $this->assertEquals($value, 4);

        $value = Arrays::get($array, 'efg');

        $this->assertEquals($value, null);
    }

    public function testAccessible()
    {
        $this->assertTrue(
            Arrays::accessible(array())
        );

        $this->assertTrue(
            Arrays::accessible(new \ArrayIterator())
        );

        $createMock = method_exists($this, 'createMock') ? 'createMock' : 'getMock';

        $this->assertTrue(
            Arrays::accessible(call_user_func(array($this, $createMock), '\ArrayAccess'))
        );

        $this->assertFalse(
            Arrays::accessible(call_user_func(array($this, $createMock), '\Traversable'))
        );

        $this->assertFalse(
            Arrays::accessible(1)
        );

        $this->assertFalse(
            Arrays::accessible('1')
        );

        $this->assertFalse(
            Arrays::accessible('string')
        );

        $this->assertFalse(
            Arrays::accessible(null)
        );

        $this->assertFalse(
            Arrays::accessible(true)
        );

        $this->assertFalse(
            Arrays::accessible(function () {
            })
        );

        $this->assertFalse(
            Arrays::accessible(new \stdClass)
        );
    }

    public function testExists()
    {
        $array = array(
            'abc' => 123,
            456
        );

        $this->assertTrue(
            Arrays::exists($array, 'abc')
        );

        $this->assertTrue(
            Arrays::exists($array, 0)
        );

        $this->assertFalse(
            Arrays::exists($array, 'efg')
        );

        $this->assertFalse(
            Arrays::exists($array, 1)
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
        $this->assertTrue(Arrays::exists($mock, 'abc'));
    }

    public function testIsIterable()
    {
        $this->assertTrue(
            Arrays::isIterable(array())
        );

        $this->assertTrue(
            Arrays::isIterable(new \ArrayIterator())
        );

        $createMock = method_exists($this, 'createMock') ? 'createMock' : 'getMock';

        $this->assertTrue(
            Arrays::isIterable(call_user_func(array($this, $createMock), '\Traversable'))
        );

        $this->assertFalse(
            Arrays::isIterable(call_user_func(array($this, $createMock), '\ArrayAccess'))
        );

        $this->assertFalse(
            Arrays::isIterable(1)
        );

        $this->assertFalse(
            Arrays::isIterable('1')
        );

        $this->assertFalse(
            Arrays::isIterable('string')
        );

        $this->assertFalse(
            Arrays::isIterable(null)
        );

        $this->assertFalse(
            Arrays::isIterable(true)
        );

        $this->assertFalse(
            Arrays::isIterable(function () {
            })
        );

        $this->assertFalse(
            Arrays::isIterable(new \stdClass)
        );
    }

    public function testTakeOffRecursive()
    {
        $array = array(
            'abc' => array(
                123,
                456,
            ),
            'efg' => array(
                123,
                456,
            ),
        );

        $value = Arrays::takeOffRecursive($array, 'abc.0');

        $this->assertThat($value, $this->isType('int'));

        $this->assertEquals($value, 123);

        $this->assertEquals(
            $array,
            array(
                'abc' => array(
                    1 => 456,
                ),
                'efg' => array(
                    123,
                    456,
                ),
            )
        );

        $value = Arrays::takeOffRecursive($array, 'efg');

        $this->assertThat($value, $this->isType('array'));

        $this->assertEquals(
            $value,
            array(
                123,
                456,
            )
        );

        $this->assertEquals(
            $array,
            array(
                'abc' => array(
                    1 => 456,
                ),
            )
        );
    }

    public function testMergeDistinctRecursive()
    {
        $this->assertEquals(
            Arrays::mergeDistinctRecursive(array(), array()),
            array()
        );

        $this->assertEquals(
            Arrays::mergeDistinctRecursive(
                array(
                    'abc' => 123,
                    'efg' => 456
                ),
                array()
            ),
            array(
                'abc' => 123,
                'efg' => 456
            )
        );

        $this->assertEquals(
            Arrays::mergeDistinctRecursive(
                array(
                    'abc' => 123,
                    'efg' => 456
                ),
                array(
                    'hij' => 789
                )
            ),
            array(
                'abc' => 123,
                'efg' => 456,
                'hij' => 789
            )
        );

        $this->assertEquals(
            Arrays::mergeDistinctRecursive(
                array(
                    'abc' => 123,
                    'efg' => 456
                ),
                array(
                    'abc' => 789,
                )
            ),
            array(
                'abc' => 789,
                'efg' => 456
            )
        );

        $this->assertEquals(
            Arrays::mergeDistinctRecursive(
                array(
                    'abc' => 123,
                    'efg' => 456
                ),
                array(
                    'abc' => 789,
                    'efg' => 123
                )
            ),
            array(
                'abc' => 789,
                'efg' => 123
            )
        );

        $this->assertEquals(
            Arrays::mergeDistinctRecursive(
                array(
                    'abc' => array(
                        'hij' => 123
                    ),
                    'efg' => 456
                ),
                array(
                    'abc' => 789,
                    'efg' => 123
                )
            ),
            array(
                'abc' => 789,
                'efg' => 123
            )
        );

        $this->assertEquals(
            Arrays::mergeDistinctRecursive(
                array(
                    'abc' => array(
                        'hij' => 123
                    ),
                    'efg' => 456
                ),
                array(
                    'abc' => array(
                        'hij' => 456,
                        'abc' => 123
                    ),
                    'efg' => 123
                )
            ),
            array(
                'abc' => array(
                    'hij' => 456,
                    'abc' => 123
                ),
                'efg' => 123
            )
        );

        $this->assertEquals(
            Arrays::mergeDistinctRecursive(
                array(
                    'abc' => array(
                        'hij' => 456,
                        'abc' => 123
                    ),
                    'efg' => 456
                ),
                array(
                    'abc' => array(
                        'hij' => 4567,
                        'abcf' => 123
                    ),
                    'efg' => 123
                )
            ),
            array(
                'abc' => array(
                    'hij' => 4567,
                    'abc' => 123,
                    'abcf' => 123
                ),
                'efg' => 123
            )
        );

        $this->assertEquals(
            Arrays::mergeDistinctRecursive(
                array(
                    array(
                        456,
                        123
                    ),
                    456
                ),
                array(
                    array(
                        4567,
                        123
                    ),
                    123
                )
            ),
            array(
                array(
                    4567,
                    123,
                ),
                123
            )
        );
    }

    public function testFields()
    {
        $array = Arrays::fields(
            array(
                'efg' => 456,
                'abc' => 123,
            ),
            array(
                123, 456
            )
        );

        $this->assertEquals(
            array_keys($array),
            array(
                'abc', 'efg'
            )
        );
    }

    public function testValue()
    {
        $this->assertThat(Arrays::value(array()), $this->isType('array'));

        $this->assertThat(Arrays::value(1), $this->isType('int'));

        $this->assertThat(Arrays::value(''), $this->isType('string'));

        $this->assertNull(
            Arrays::value(null)
        );

        $this->assertNull(
            Arrays::value(function () {
            })
        );

        $this->assertThat(Arrays::value(function () {
            return array();
        }), $this->isType('array'));

        $this->assertThat(Arrays::value(function () {
            return 123;
        }), $this->isType('int'));

        $this->assertThat(Arrays::value(function () {
            return '';
        }), $this->isType('string'));

        $this->assertThat(Arrays::value(function ($array) {
            return $array;
        }, array()), $this->isType('array'));

        $this->assertThat(Arrays::value(function ($int) {
            return $int;
        }, 123), $this->isType('int'));

        $this->assertThat(Arrays::value(function ($string) {
            return $string;
        }, ''), $this->isType('string'));
    }

    public function testCollapse()
    {
        $this->assertEquals(
            Arrays::collapse(array(
                array(1, 2, 3),
                array(4, 5, 6),
                array(7, 8, 9),
            )),
            array(1, 2, 3, 4, 5, 6, 7, 8, 9)
        );
    }

    public function testReduce()
    {
        $this->assertEquals(
            Arrays::reduce(
                array(123, 456),
                function ($carry, $item) {
                    if (!$carry) {
                        $carry = array();
                    }

                    array_push($carry, $item);

                    return $carry;
                }
            ),
            array(123, 456)
        );
    }

    public function testFilter()
    {
        $array = Arrays::filter(
            array(
                123, null, '', 0, 456, 'abc' => null, 'efg' => '', 'hij' => 0
            ),
            function ($item, $index) {
                $this->assertTrue(in_array($index, array(0, 1, 2, 3, 4, 'abc', 'efg', 'hij')));

                $this->assertTrue(in_array($item, array(123, null, '', 0, 456)));

                $this->assertTrue(in_array($item, array('123', null, '', '0', '456')));

                return $item;
            }
        );

        $this->assertEquals(
            $array,
            array(
                0 => 123,
                4 => 456,
            )
        );
    }

    public function testForget()
    {
        $array = array(
            'abc' => 123,
            'efg' => 456
        );

        Arrays::forget(
            $array,
            'abc'
        );

        $this->assertArrayHasKey('efg', $array);

        $this->assertEquals(
            $array,
            array(
                'efg' => 456
            )
        );

        $array = Arrays::except(
            array(
                'abc' => 123,
                'efg' => 456
            ),
            array(
                'abc',
            )
        );

        $this->assertArrayHasKey('efg', $array);

        $this->assertEquals(
            $array,
            array(
                'efg' => 456
            )
        );
    }

    public function testCamel()
    {
        $this->assertEquals(
            Arrays::keyCamel(
                array(
                    'abc_efg' => 123,
                    'hij_klm' => 456,
                )
            ),
            array(
                'abcEfg' => 123,
                'hijKlm' => 456,
            )
        );
    }

    public function testKeySnakeToCamel()
    {
        $this->assertEquals(
            Arrays::keySnakeToCamel(
                array(
                    'abc_efg' => 123,
                    'hij_klm' => 456,
                )
            ),
            array(
                'abcEfg' => 123,
                'hijKlm' => 456,
            )
        );
    }

    public function testKeyKebabCaseToCamel()
    {
        $this->assertEquals(
            Arrays::keyKebabCaseToCamel(
                array(
                    'abc-efg' => 123,
                    'hij-klm' => 456,
                )
            ),
            array(
                'abcEfg' => 123,
                'hijKlm' => 456,
            )
        );
    }

    public function testReplace()
    {
        $this->assertEquals(
            Arrays::replace(
                array(
                    'abcEfg' => 123,
                    'hijKlm' => 456,
                ),
                array(
                    'abcEfg' => 456,
                    'hijKlm' => 123,
                )
            ),
            array(
                'abcEfg' => 456,
                'hijKlm' => 123,
            )
        );

        $this->assertEquals(
            Arrays::replace(
                array(
                    'abcEfg' => 123,
                    'hijKlm' => 456,
                    'nopQrs' => array(
                        'tuvWxy' => 789,
                        'zabcde' => 123,
                    ),
                ),
                array(
                    'abcEfg' => 456,
                    'hijKlm' => 123,
                    'nopQrs' => array(
                        'zabcde' => 789,
                    ),
                )
            ),
            array(
                'abcEfg' => 456,
                'hijKlm' => 123,
                'nopQrs' => array(
                    'zabcde' => 789,
                ),
            )
        );
    }

    public function testReplaceRecursive()
    {
        $this->assertEquals(
            Arrays::replaceRecursive(
                array(
                    'abcEfg' => 123,
                    'hijKlm' => 456,
                ),
                array(
                    'abcEfg' => 456,
                    'hijKlm' => 123,
                )
            ),
            array(
                'abcEfg' => 456,
                'hijKlm' => 123,
            )
        );

        $this->assertEquals(
            Arrays::replaceRecursive(
                array(
                    'abcEfg' => 123,
                    'hijKlm' => 456,
                    'nopQrs' => array(
                        'tuvWxy' => 789,
                        'zabcde' => 123,
                    ),
                ),
                array(
                    'abcEfg' => 456,
                    'hijKlm' => 123,
                    'nopQrs' => array(
                        'zabcde' => 789,
                    ),
                )
            ),
            array(
                'abcEfg' => 456,
                'hijKlm' => 123,
                'nopQrs' => array(
                    'tuvWxy' => 789,
                    'zabcde' => 789,
                ),
            )
        );
    }

    public function testFirst()
    {
        $this->assertEquals(
            Arrays::first(
                array(
                    array(
                        'abcEfg' => 123,
                        'hijKlm' => 456,
                    ),
                    array(
                        'abcEfg' => 456,
                        'hijKlm' => 123,
                    ),
                )
            ),
            array(
                'abcEfg' => 123,
                'hijKlm' => 456,
            )
        );

        $this->assertEquals(
            Arrays::first(
                array(
                    'abcEfg' => 123,
                    'hijKlm' => 456,
                )
            ),
            123
        );

        $this->assertEquals(
            Arrays::first(
                array(1, 2, 3, 4, 5, 6),
                function ($value) {
                    return $value > 4;
                }
            ),
            5
        );

        $this->assertEquals(
            Arrays::first(
                array(1, 2, 3, 4, 5, 6),
                function ($value) {
                    return $value > 6;
                },
                7
            ),
            7
        );
    }

    public function testLast()
    {
        $this->assertEquals(
            Arrays::last(
                array(
                    array(
                        'abcEfg' => 123,
                        'hijKlm' => 456,
                    ),
                    array(
                        'abcEfg' => 456,
                        'hijKlm' => 123,
                    ),
                )
            ),
            array(
                'abcEfg' => 456,
                'hijKlm' => 123,
            )
        );

        $this->assertEquals(
            Arrays::last(
                array(
                    'abcEfg' => 123,
                    'hijKlm' => 456,
                )
            ),
            456
        );

        $this->assertEquals(
            Arrays::last(
                array(1, 2, 3, 4, 5, 6),
                function ($value) {
                    return $value > 4;
                }
            ),
            6
        );

        $this->assertEquals(
            Arrays::last(
                array(1, 2, 3, 4, 5, 6),
                function ($value) {
                    return $value > 6;
                },
                7
            ),
            7
        );
    }

    public function testDivide()
    {
        $this->assertEquals(
            Arrays::divide(
                array(
                    'abcEfg' => 123,
                    'hijKlm' => 456,
                )
            ),
            array(
                array(
                    'abcEfg',
                    'hijKlm',
                ),
                array(
                    123,
                    456,
                ),
            )
        );
    }

    public function testDot()
    {
        $this->assertEquals(
            Arrays::dot(
                array(
                    'abcEfg' => array(
                        'hijKlm' => 123,
                    ),
                )
            ),
            array(
                'abcEfg.hijKlm' => 123
            )
        );
    }

    public function testUndot()
    {
        $this->assertEquals(
            Arrays::undot(
                array(
                    'abcEfg.hijKlm' => 123
                )
            ),
            array(
                'abcEfg' => array(
                    'hijKlm' => 123,
                ),
            )
        );
    }

    public function testHas()
    {
        $this->assertTrue(
            Arrays::has(
                array(
                    'abcEfg' => 123,
                ),
                'abcEfg'
            )
        );
    }

    public function testFlatten()
    {
        $this->assertEquals(
            Arrays::flatten(
                array(
                    'abcEfg' => 123,
                    array(
                        'abcEfg' => 456,
                        'hijKlm' => 123,
                    ),
                )
            ),
            array(123, 456, 123)
        );
    }

    public function testPrepend()
    {
        $this->assertEquals(
            Arrays::prepend(
                array(
                    'abcEfg' => 123,
                    array(
                        'abcEfg' => 456,
                        'hijKlm' => 123,
                    ),
                ),
                456,
                'hijKlm'
            ),
            array(
                'hijKlm' => 456,
                'abcEfg' => 123,
                array(
                    'abcEfg' => 456,
                    'hijKlm' => 123,
                ),
            )
        );
    }

    public function testPull()
    {
        $array = array(
            'abcEfg' => 123,
            array(
                'abcEfg' => 456,
                'hijKlm' => 123,
            ),
        );

        $this->assertEquals(
            Arrays::pull(
                $array,
                'abcEfg'
            ),
            123
        );

        $this->assertEquals(
            $array,
            array(
                array(
                    'abcEfg' => 456,
                    'hijKlm' => 123,
                )
            )
        );
    }

    public function testWrap()
    {
        $this->assertEquals(
            Arrays::wrap(
                123
            ),
            array(123)
        );

        $this->assertEquals(
            Arrays::wrap(
                array(123)
            ),
            array(123)
        );

        $this->assertEquals(
            Arrays::wrap(
                null
            ),
            array()
        );
    }
}
