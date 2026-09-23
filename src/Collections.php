<?php

namespace Wilkques\Helpers;

// SORT_NATURAL/SORT_FLAG_CASE were only added in PHP 5.4.0.
if (!defined('SORT_NATURAL')) {
    define('SORT_NATURAL', 6);
}

if (!defined('SORT_FLAG_CASE')) {
    define('SORT_FLAG_CASE', 8);
}

class Collections implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var array
     */
    protected $items;

    /**
     * @param mixed $items
     */
    public function __construct($items = array())
    {
        $this->items = $this->getArrayableItems($items);
    }

    /**
     * Create a new collection instance.
     *
     * @param  mixed  $items
     * @return static
     */
    public static function make($items = array())
    {
        return new static($items);
    }

    /**
     * Results array of items from Collection or Arrayable.
     *
     * @param  mixed  $items
     * @return array
     */
    protected function getArrayableItems($items)
    {
        if ($items instanceof self) {
            return $items->all();
        }

        if (is_array($items)) {
            return $items;
        }

        if (is_string($items)) {
            list($array, $isError) = $this->json($items);

            if (!$isError) {
                return $array;
            }
        }

        if ($items instanceof \JsonSerializable) {
            return (array) $items->jsonSerialize();
        }

        if ($items instanceof \Traversable) {
            return iterator_to_array($items);
        }

        return (array) $items;
    }

    /**
     * @param string $json
     *
     * @return array
     */
    protected function json($json)
    {
        if (!is_string($json)) {
            return array(array(), false);
        }

        $decode = json_decode($json, true);

        $hasError = (json_last_error() !== JSON_ERROR_NONE);

        return array($decode, $hasError);
    }

    /**
     * Get the underlying items array.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Recursively convert the collection (and any nested collections) to a plain array.
     *
     * @return array
     */
    public function toArray()
    {
        $result = array();

        foreach ($this->items as $key => $value) {
            $result[$key] = $value instanceof self ? $value->toArray() : $value;
        }

        return $result;
    }

    /**
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->items);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return !$this->isEmpty();
    }

    /**
     * @param callback|\Closure|null $callback
     *
     * @return static
     */
    public function map($callback)
    {
        return new static(Arrays::map($this->items, $callback));
    }

    /**
     * @param callable $callback
     *
     * @return static
     */
    public function mapWithKeys($callback)
    {
        return new static(Arrays::mapWithKeys($this->items, $callback));
    }

    /**
     * @param callable|null $callback
     *
     * @return static
     */
    public function filter($callback = null)
    {
        return new static(Arrays::filter($this->items, $callback));
    }

    /**
     * @param callable $callback
     *
     * @return static
     */
    public function reject($callback)
    {
        return new static(Arrays::filter($this->items, function ($value, $key) use ($callback) {
            return !call_user_func($callback, $value, $key);
        }));
    }

    /**
     * @return static
     */
    public function whereNotNull()
    {
        return new static(Arrays::whereNotNull($this->items));
    }

    /**
     * @param string $value
     * @param string|null $key
     *
     * @return static
     */
    public function pluck($value, $key = null)
    {
        return new static(Arrays::pluck($this->items, $value, $key));
    }

    /**
     * @return static
     */
    public function values()
    {
        return new static(array_values($this->items));
    }

    /**
     * @return static
     */
    public function keys()
    {
        return new static(array_keys($this->items));
    }

    /**
     * @return static
     */
    public function flip()
    {
        return new static(array_flip($this->items));
    }

    /**
     * @param array|string $keys
     *
     * @return static
     */
    public function only($keys)
    {
        return new static(Arrays::only($this->items, $keys));
    }

    /**
     * @param array|string $keys
     *
     * @return static
     */
    public function except($keys)
    {
        return new static(Arrays::except($this->items, $keys));
    }

    /**
     * @param mixed $items
     *
     * @return static
     */
    public function merge($items)
    {
        return new static(array_merge($this->items, $this->getArrayableItems($items)));
    }

    /**
     * @param callable|string|null $key
     *
     * @return static
     */
    public function unique($key = null)
    {
        if (is_null($key)) {
            return new static(array_unique($this->items, SORT_REGULAR));
        }

        $exists = array();

        return $this->filter(function ($item) use ($key, &$exists) {
            $id = is_callable($key) ? call_user_func($key, $item) : Objects::get($item, $key);

            if (in_array($id, $exists, true)) {
                return false;
            }

            $exists[] = $id;

            return true;
        });
    }

    /**
     * @param int|float $depth
     *
     * @return static
     */
    public function flatten($depth = INF)
    {
        return new static(Arrays::flatten($this->items, $depth));
    }

    /**
     * @return static
     */
    public function collapse()
    {
        return new static(Arrays::collapse($this->items));
    }

    /**
     * @param callable|string|null $callback
     *
     * @return static
     */
    public function sort($callback = null)
    {
        return new static(Arrays::sort($this->items, $callback));
    }

    /**
     * @param callable|string|null $callback
     *
     * @return static
     */
    public function sortDesc($callback = null)
    {
        return new static(Arrays::sortDesc($this->items, $callback));
    }

    /**
     * @param callable|string|array $callback A single key/callable, or an
     *  array of them (optionally as [key, 'asc'|'desc'] pairs) for
     *  multi-column sorting with tie-breaking.
     * @param int $options One of the SORT_* flags.
     * @param bool $descending
     *
     * @return static
     */
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false)
    {
        if (is_array($callback) && !is_callable($callback)) {
            return $this->sortByMany($callback, $options);
        }

        $decorated = array();
        $sequence = 0;

        foreach ($this->items as $key => $value) {
            $sortValue = is_callable($callback) ? call_user_func($callback, $value, $key) : Objects::get($value, $callback);

            $decorated[] = array('key' => $key, 'value' => $value, 'sort' => $sortValue, 'seq' => $sequence);

            $sequence++;
        }

        $that = $this;

        usort($decorated, function ($a, $b) use ($that, $options, $descending) {
            $result = $that->compareSortValues($a['sort'], $b['sort'], $options);

            if ($descending) {
                $result = -$result;
            }

            if ($result === 0) {
                return $a['seq'] < $b['seq'] ? -1 : 1;
            }

            return $result;
        });

        $results = array();

        foreach ($decorated as $item) {
            $results[$item['key']] = $item['value'];
        }

        return new static($results);
    }

    /**
     * Sort by multiple [key, direction] comparisons, falling through to the
     * next comparison whenever the previous one ties. Ties across every
     * comparison break on original position, so the result is stable
     * regardless of whether the running PHP version's sort() is (PHP's own
     * sort stability was only guaranteed starting with PHP 8.0.0, and this
     * package supports PHP 5.3+).
     *
     * @param array $comparisons
     * @param int $options
     *
     * @return static
     */
    protected function sortByMany($comparisons = array(), $options = SORT_REGULAR)
    {
        $decorated = array();
        $sequence = 0;

        foreach ($this->items as $key => $value) {
            $decorated[] = array('key' => $key, 'value' => $value, 'seq' => $sequence);

            $sequence++;
        }

        $that = $this;

        usort($decorated, function ($a, $b) use ($that, $comparisons, $options) {
            foreach ($comparisons as $comparison) {
                $comparison = Arrays::wrap($comparison);

                $prop = $comparison[0];

                $ascending = !isset($comparison[1]) || $comparison[1] === true || $comparison[1] === 'asc';

                if (!is_string($prop) && is_callable($prop)) {
                    $result = call_user_func($prop, $a['value'], $b['value']);
                } else {
                    $result = $that->compareSortValues(Objects::get($a['value'], $prop), Objects::get($b['value'], $prop), $options);

                    if (!$ascending) {
                        $result = -$result;
                    }
                }

                if ($result === 0) {
                    continue;
                }

                return $result;
            }

            return $a['seq'] < $b['seq'] ? -1 : 1;
        });

        $results = array();

        foreach ($decorated as $item) {
            $results[$item['key']] = $item['value'];
        }

        return new static($results);
    }

    /**
     * @param mixed $aValue
     * @param mixed $bValue
     * @param int $options One of the SORT_* flags.
     *
     * @return int -1, 0, or 1
     */
    public function compareSortValues($aValue, $bValue, $options)
    {
        if (($options & SORT_FLAG_CASE) === SORT_FLAG_CASE) {
            return (($options & SORT_NATURAL) === SORT_NATURAL)
                ? strnatcasecmp($aValue, $bValue)
                : strcasecmp($aValue, $bValue);
        }

        switch ($options) {
            case SORT_NUMERIC:
                $left = intval($aValue);
                $right = intval($bValue);
                return $left == $right ? 0 : ($left < $right ? -1 : 1);
            case SORT_STRING:
                return strcmp($aValue, $bValue);
            case SORT_NATURAL:
                return strnatcmp((string) $aValue, (string) $bValue);
            case SORT_LOCALE_STRING:
                return strcoll($aValue, $bValue);
            default:
                return $aValue == $bValue ? 0 : ($aValue < $bValue ? -1 : 1);
        }
    }

    /**
     * @param callable|string|array $callback
     * @param int $options
     *
     * @return static
     */
    public function sortByDesc($callback, $options = SORT_REGULAR)
    {
        if (is_array($callback) && !is_callable($callback)) {
            foreach ($callback as $index => $key) {
                $comparison = Arrays::wrap($key);
                $comparison[1] = 'desc';
                $callback[$index] = $comparison;
            }

            return $this->sortByMany($callback, $options);
        }

        return $this->sortBy($callback, $options, true);
    }

    /**
     * @param int $size
     *
     * @return static
     */
    public function chunk($size)
    {
        if ($size <= 0) {
            return new static();
        }

        $chunks = array();

        foreach (array_chunk($this->items, $size, true) as $chunk) {
            $chunks[] = new static($chunk);
        }

        return new static($chunks);
    }

    /**
     * Group by a single key/callable, or by an array of them for
     * multi-level nested grouping (each level's retriever may also return
     * an array of group keys, putting a single item into multiple groups).
     *
     * Does not special-case enum/Stringable group keys (PHP 8.1+ concepts,
     * not applicable at this package's PHP 5.3 floor); pass a callable
     * that returns a plain scalar for those cases instead.
     *
     * @param callable|string|array $groupBy
     * @param bool $preserveKeys
     *
     * @return static
     */
    public function groupBy($groupBy, $preserveKeys = false)
    {
        $nextGroups = array();

        if (!is_callable($groupBy) && is_array($groupBy)) {
            $nextGroups = $groupBy;
            $groupBy = array_shift($nextGroups);
        }

        $results = array();

        foreach ($this->items as $key => $value) {
            $groupKeys = is_callable($groupBy) ? call_user_func($groupBy, $value, $key) : Objects::get($value, $groupBy);

            if (!is_array($groupKeys)) {
                $groupKeys = array($groupKeys);
            }

            foreach ($groupKeys as $groupKey) {
                $groupKey = is_bool($groupKey) ? (int) $groupKey : $groupKey;

                if (!array_key_exists($groupKey, $results)) {
                    $results[$groupKey] = new static();
                }

                $results[$groupKey]->offsetSet($preserveKeys ? $key : null, $value);
            }
        }

        $result = new static($results);

        if (!empty($nextGroups)) {
            return $result->map(function ($group) use ($nextGroups, $preserveKeys) {
                return $group->groupBy($nextGroups, $preserveKeys);
            });
        }

        return $result;
    }

    /**
     * @param callable|string $keyBy
     *
     * @return static
     */
    public function keyBy($keyBy)
    {
        $results = array();

        foreach ($this->items as $key => $item) {
            $resolvedKey = is_callable($keyBy) ? call_user_func($keyBy, $item, $key) : Objects::get($item, $keyBy);

            $results[$resolvedKey] = $item;
        }

        return new static($results);
    }

    /**
     * @param string $value
     * @param string|null $glue
     *
     * @return string
     */
    public function implode($value, $glue = null)
    {
        $first = $this->first();

        if (is_array($first) || (is_object($first) && !method_exists($first, '__toString'))) {
            return implode(is_null($glue) ? '' : $glue, $this->pluck($value)->all());
        }

        return implode(is_null($value) ? '' : $value, $this->items);
    }

    /**
     * @param callback|\Closure $callback
     * @param mixed $initial
     *
     * @return mixed
     */
    public function reduce($callback, $initial = null)
    {
        $result = $initial;

        foreach ($this->items as $key => $value) {
            $result = call_user_func($callback, $result, $value, $key);
        }

        return $result;
    }

    /**
     * @param callable $callback
     *
     * @return static
     */
    public function each($callback)
    {
        foreach ($this->items as $key => $item) {
            if (call_user_func($callback, $item, $key) === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * @param callback|\Closure|null $callback
     * @param mixed $default
     *
     * @return mixed
     */
    public function first($callback = null, $default = null)
    {
        return Arrays::first($this->items, $callback, $default);
    }

    /**
     * @param callback|\Closure|null $callback
     * @param mixed $default
     *
     * @return mixed
     */
    public function last($callback = null, $default = null)
    {
        return Arrays::last($this->items, $callback, $default);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return bool
     */
    public function contains($key, $value = null)
    {
        if (func_num_args() === 1) {
            if (!is_string($key) && is_callable($key)) {
                foreach ($this->items as $itemKey => $item) {
                    if (call_user_func($key, $item, $itemKey)) {
                        return true;
                    }
                }

                return false;
            }

            return in_array($key, $this->items);
        }

        foreach ($this->items as $item) {
            if (Objects::get($item, $key) == $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string|int|null $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arrays::get($this->items, $key, $default);
    }

    /**
     * @param string|array $key
     *
     * @return bool
     */
    public function has($key)
    {
        return Arrays::has($this->items, $key);
    }

    /**
     * @param string|null $key
     * @param mixed $value
     *
     * @return static
     */
    public function set($key, $value)
    {
        Arrays::set($this->items, $key, $value);

        return $this;
    }

    /**
     * @param string|null $key
     * @param mixed $value
     *
     * @return static
     */
    public function put($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * @param array|string $keys
     *
     * @return static
     */
    public function forget($keys)
    {
        Arrays::forget($this->items, $keys);

        return $this;
    }

    /**
     * @param string|int $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function pull($key, $default = null)
    {
        return Arrays::pull($this->items, $key, $default);
    }

    /**
     * @param mixed $value
     * @param mixed $key
     *
     * @return static
     */
    public function prepend($value, $key = null)
    {
        if (func_num_args() === 1) {
            $this->items = Arrays::prepend($this->items, $value);
        } else {
            $this->items = Arrays::prepend($this->items, $value, $key);
        }

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return static
     */
    public function push($value)
    {
        $this->items[] = $value;

        return $this;
    }

    /**
     * @return \ArrayIterator
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @param string|int $offset
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return Arrays::exists($this->items, $offset);
    }

    /**
     * @param string|int $offset
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * @param string|int|null $offset
     * @param mixed $value
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * @param string|int $offset
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}
