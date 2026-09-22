<?php

namespace Wilkques\Helpers\Tests;

use PHPUnit\Framework\TestCase;
use Wilkques\Helpers\Collections;

class CollectionsTest extends TestCase
{
    public function testConstructFromArray()
    {
        $collection = new Collections(array(1, 2, 3));

        $this->assertEquals(array(1, 2, 3), $collection->all());
    }

    public function testConstructFromJson()
    {
        $collection = new Collections('{"abc":123,"efg":456}');

        $this->assertEquals(array('abc' => 123, 'efg' => 456), $collection->all());
    }

    public function testConstructFromTraversable()
    {
        $collection = new Collections(new \ArrayIterator(array(1, 2, 3)));

        $this->assertEquals(array(1, 2, 3), $collection->all());
    }

    public function testConstructFromCollection()
    {
        $collection = new Collections(new Collections(array(1, 2, 3)));

        $this->assertEquals(array(1, 2, 3), $collection->all());
    }

    public function testMake()
    {
        $collection = Collections::make(array(1, 2, 3));

        $this->assertInstanceOf('Wilkques\Helpers\Collections', $collection);

        $this->assertEquals(array(1, 2, 3), $collection->all());
    }

    public function testToArrayRecursive()
    {
        $collection = new Collections(array(
            'abc' => new Collections(array(1, 2)),
            'efg' => 3,
        ));

        $this->assertEquals(
            array('abc' => array(1, 2), 'efg' => 3),
            $collection->toArray()
        );
    }

    public function testToJson()
    {
        $collection = new Collections(array('abc' => 123));

        $this->assertEquals('{"abc":123}', $collection->toJson());
    }

    public function testCount()
    {
        $collection = new Collections(array(1, 2, 3));

        $this->assertEquals(3, $collection->count());

        $this->assertCount(3, $collection);
    }

    public function testIsEmpty()
    {
        $empty = new Collections();

        $this->assertTrue($empty->isEmpty());

        $this->assertFalse($empty->isNotEmpty());

        $notEmpty = new Collections(array(1));

        $this->assertTrue($notEmpty->isNotEmpty());
    }

    public function testMapDoesNotMutateOriginal()
    {
        $collection = new Collections(array(1, 2, 3));

        $mapped = $collection->map(function ($item) {
            return $item * 2;
        });

        $this->assertEquals(array(1, 2, 3), $collection->all());

        $this->assertEquals(array(2, 4, 6), $mapped->all());
    }

    public function testMapWithKeys()
    {
        $collection = new Collections(array(
            array('id' => 1, 'name' => 'a'),
            array('id' => 2, 'name' => 'b'),
        ));

        $mapped = $collection->mapWithKeys(function ($item) {
            return array($item['id'] => $item['name']);
        });

        $this->assertEquals(array(1 => 'a', 2 => 'b'), $mapped->all());
    }

    public function testFilter()
    {
        $collection = new Collections(array(1, 2, 3, 4));

        $filtered = $collection->filter(function ($item) {
            return $item % 2 === 0;
        });

        $this->assertEquals(array(1 => 2, 3 => 4), $filtered->all());
    }

    public function testReject()
    {
        $collection = new Collections(array(1, 2, 3, 4));

        $rejected = $collection->reject(function ($item) {
            return $item % 2 === 0;
        });

        $this->assertEquals(array(0 => 1, 2 => 3), $rejected->all());
    }

    public function testWhereNotNull()
    {
        $collection = new Collections(array(1, null, 2));

        $this->assertEquals(array(0 => 1, 2 => 2), $collection->whereNotNull()->all());
    }

    public function testPluck()
    {
        $collection = new Collections(array(
            array('id' => 1, 'name' => 'a'),
            array('id' => 2, 'name' => 'b'),
        ));

        $this->assertEquals(array('a', 'b'), $collection->pluck('name')->all());

        $this->assertEquals(array(1 => 'a', 2 => 'b'), $collection->pluck('name', 'id')->all());
    }

    public function testValuesAndKeys()
    {
        $collection = new Collections(array('abc' => 1, 'efg' => 2));

        $this->assertEquals(array(1, 2), $collection->values()->all());

        $this->assertEquals(array('abc', 'efg'), $collection->keys()->all());
    }

    public function testFlip()
    {
        $collection = new Collections(array('abc' => 1, 'efg' => 2));

        $this->assertEquals(array(1 => 'abc', 2 => 'efg'), $collection->flip()->all());
    }

    public function testOnlyExcept()
    {
        $collection = new Collections(array('abc' => 1, 'efg' => 2));

        $this->assertEquals(array('abc' => 1), $collection->only('abc')->all());

        $this->assertEquals(array('efg' => 2), $collection->except('abc')->all());
    }

    public function testMerge()
    {
        $collection = new Collections(array('abc' => 1));

        $merged = $collection->merge(array('efg' => 2));

        $this->assertEquals(array('abc' => 1, 'efg' => 2), $merged->all());

        $this->assertEquals(array('abc' => 1), $collection->all());
    }

    public function testUnique()
    {
        $collection = new Collections(array(1, 2, 2, 3, 1));

        $this->assertEquals(array(0 => 1, 1 => 2, 3 => 3), $collection->unique()->all());

        $collection = new Collections(array(
            array('id' => 1),
            array('id' => 1),
            array('id' => 2),
        ));

        $unique = $collection->unique('id');

        $this->assertEquals(array(0, 2), array_keys($unique->all()));
    }

    public function testFlattenAndCollapse()
    {
        $collection = new Collections(array('abc' => 1, array(2, 3)));

        $this->assertEquals(array(1, 2, 3), $collection->flatten()->all());

        $collection = new Collections(array(array(1, 2), array(3, 4)));

        $this->assertEquals(array(1, 2, 3, 4), $collection->collapse()->all());
    }

    public function testSortAndSortDesc()
    {
        $collection = new Collections(array(3, 1, 2));

        $this->assertEquals(array(1, 2, 3), array_values($collection->sort()->all()));

        $this->assertEquals(array(3, 2, 1), array_values($collection->sortDesc()->all()));
    }

    public function testSortByAndSortByDesc()
    {
        $collection = new Collections(array(
            array('abc' => 3),
            array('abc' => 1),
            array('abc' => 2),
        ));

        $sorted = $collection->sortBy('abc');

        $this->assertEquals(array(1, 2, 3), array_values($sorted->pluck('abc')->all()));

        $sortedDesc = $collection->sortByDesc('abc');

        $this->assertEquals(array(3, 2, 1), array_values($sortedDesc->pluck('abc')->all()));
    }

    public function testChunk()
    {
        $collection = new Collections(array(1, 2, 3, 4, 5));

        $chunks = $collection->chunk(2);

        $this->assertEquals(3, $chunks->count());

        $this->assertEquals(array(1, 2), $chunks->get(0)->all());

        $this->assertEquals(array(3, 4), array_values($chunks->get(1)->all()));

        $this->assertEquals(array(5), array_values($chunks->get(2)->all()));
    }

    public function testGroupBy()
    {
        $collection = new Collections(array(
            array('type' => 'a', 'value' => 1),
            array('type' => 'b', 'value' => 2),
            array('type' => 'a', 'value' => 3),
        ));

        $grouped = $collection->groupBy('type');

        $this->assertEquals(array(1, 3), array_values($grouped->get('a')->pluck('value')->all()));

        $this->assertEquals(array(2), array_values($grouped->get('b')->pluck('value')->all()));
    }

    public function testKeyBy()
    {
        $collection = new Collections(array(
            array('id' => 1, 'name' => 'a'),
            array('id' => 2, 'name' => 'b'),
        ));

        $keyed = $collection->keyBy('id');

        $first = $keyed->get(1);

        $second = $keyed->get(2);

        $this->assertEquals('a', $first['name']);

        $this->assertEquals('b', $second['name']);
    }

    public function testImplode()
    {
        $collection = new Collections(array('a', 'b', 'c'));

        $this->assertEquals('a,b,c', $collection->implode(','));

        $collection = new Collections(array(
            array('name' => 'a'),
            array('name' => 'b'),
        ));

        $this->assertEquals('a,b', $collection->implode('name', ','));
    }

    public function testReduce()
    {
        $collection = new Collections(array(1, 2, 3));

        $this->assertEquals(6, $collection->reduce(function ($carry, $item) {
            return $carry + $item;
        }, 0));
    }

    public function testReducePassesKeyToCallback()
    {
        $collection = new Collections(array('a' => 1, 'b' => 2));

        $seenKeys = array();

        $collection->reduce(function ($carry, $item, $key) use (&$seenKeys) {
            $seenKeys[] = $key;

            return $carry + $item;
        }, 0);

        $this->assertEquals(array('a', 'b'), $seenKeys);
    }

    public function testEach()
    {
        $collection = new Collections(array(1, 2, 3));

        $seen = array();

        $collection->each(function ($item) use (&$seen) {
            $seen[] = $item;

            if ($item === 2) {
                return false;
            }
        });

        $this->assertEquals(array(1, 2), $seen);
    }

    public function testFirstAndLast()
    {
        $collection = new Collections(array(1, 2, 3));

        $this->assertEquals(1, $collection->first());

        $this->assertEquals(3, $collection->last());

        $this->assertEquals(2, $collection->first(function ($item) {
            return $item > 1;
        }));
    }

    public function testContains()
    {
        $collection = new Collections(array(1, 2, 3));

        $this->assertTrue($collection->contains(2));

        $this->assertFalse($collection->contains(4));

        $this->assertTrue($collection->contains(function ($item) {
            return $item > 2;
        }));

        $collection = new Collections(array(
            array('id' => 1, 'name' => 'a'),
        ));

        $this->assertTrue($collection->contains('name', 'a'));

        $this->assertFalse($collection->contains('name', 'b'));
    }

    public function testContainsWithPlainStringDoesNotTreatItAsCallable()
    {
        // 'strlen' is_callable() === true (resolves to the global
        // function), but contains() must still search for it as a literal
        // value, not invoke it as a predicate.
        $collection = new Collections(array('strlen', 'trim'));

        $this->assertTrue($collection->contains('strlen'));

        $this->assertFalse($collection->contains('nope'));
    }

    public function testGetHasSetForgetPull()
    {
        $collection = new Collections(array('abc' => 1));

        $this->assertTrue($collection->has('abc'));

        $this->assertEquals(1, $collection->get('abc'));

        $this->assertNull($collection->get('efg'));

        $collection->set('efg', 2);

        $this->assertEquals(2, $collection->get('efg'));

        $collection->put('hij', 3);

        $this->assertEquals(3, $collection->get('hij'));

        $pulled = $collection->pull('hij');

        $this->assertEquals(3, $pulled);

        $this->assertFalse($collection->has('hij'));

        $collection->forget('abc');

        $this->assertFalse($collection->has('abc'));
    }

    public function testPrependAndPush()
    {
        $collection = new Collections(array(1, 2));

        $collection->push(3);

        $this->assertEquals(array(1, 2, 3), $collection->all());

        $collection->prepend(0);

        $this->assertEquals(array(0, 1, 2, 3), $collection->all());
    }

    public function testArrayAccess()
    {
        $collection = new Collections(array('abc' => 1));

        $this->assertTrue(isset($collection['abc']));

        $this->assertEquals(1, $collection['abc']);

        $collection['efg'] = 2;

        $this->assertEquals(2, $collection['efg']);

        unset($collection['abc']);

        $this->assertFalse(isset($collection['abc']));
    }

    public function testIteratorAggregate()
    {
        $collection = new Collections(array(1, 2, 3));

        $seen = array();

        foreach ($collection as $item) {
            $seen[] = $item;
        }

        $this->assertEquals(array(1, 2, 3), $seen);
    }

    public function testCollectHelper()
    {
        $collection = collect(array(1, 2, 3));

        $this->assertInstanceOf('Wilkques\Helpers\Collections', $collection);

        $this->assertEquals(array(1, 2, 3), $collection->all());
    }
}
