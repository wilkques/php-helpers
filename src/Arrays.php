<?php

namespace Wilkques\Helpers;

use ArrayAccess;

class Arrays
{
    /**
     * @param  array  $array
     * @param  array|string  $keys
     * 
     * @return array
     */
    public static function only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    /**
     * String snake to study
     * 
     * @param array $array
     * 
     * @return array
     */
    public static function keySnake($array)
    {
        return array_combine(
            array_map(array('Wilkques\Helpers\Strings', 'snake'), array_keys($array)),
            $array
        );
    }

    /**
     * @param array $array
     * @param callback|\Closure|null $callback
     * 
     * @return array
     */
    public static function map($array, $callback = null)
    {
        $keys = array_keys($array);

        $items = array_map($callback, $array, $keys);

        return array_combine($keys, $items);
    }

    /**
     * @param array $array
     * @param string $value
     * @param string|null $key
     * @param int|string|null $case Either upper|lower|snake|Kebab|camel|null
     * 
     * @return array
     */
    public static function pluck($array, $value, $key = null, $case = null)
    {
        $results = array();

        $value = is_string($value) ? explode('.', $value) : $value;

        $key = is_null($key) || is_array($key) ? $key : explode('.', $key);

        foreach ($array as $item) {
            $itemValue = data_get($item, $value);

            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = data_get($item, $key);

                if (is_object($itemKey) && method_exists($itemKey, '__toString')) {
                    $itemKey = (string) $itemKey;
                }

                switch ($case) {
                    case 'lower':
                        $itemKey = Strings::lower($itemKey);
                        break;
                    case 'upper':
                        $itemKey = Strings::upper($itemKey);
                        break;
                    case 'snake':
                        $itemKey = Strings::snake($itemKey);
                        break;
                    case 'kebab':
                        $itemKey = Strings::Kebab($itemKey);
                        break;
                    case 'camel':
                        $itemKey = Strings::camel($itemKey);
                        break;
                }

                $results[$itemKey] = $itemValue;
            }
        }

        return $results;
    }

    /**
     * @param array $array
     * @param callable $callback
     * 
     * @return array
     */
    public static function mapWithKeys($array, $callback)
    {
        $result = array();

        foreach ($array as $key => $value) {
            $assoc = $callback($value, $key);

            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return $result;
    }

    /**
     * @param array $array
     * @param callback|\Closure $callback
     * 
     * @return array
     */
    public static function where($array, $callback)
    {
        return static::filter($array, $callback);
    }

    /**
     * Get all of the given array except for a specified array of keys.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return array
     */
    public static function except($array, $keys)
    {
        static::forget($array, $keys);

        return $array;
    }

    /**
     * @param array $array
     * @param array $sort
     * 
     * @return array
     */
    public static function keyFields($array, $sort)
    {
        return array_replace(array_flip($sort), $array);
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array  $array
     * @param  string|null  $key
     * @param  mixed  $value
     * @return array
     */
    public static function set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = array();
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        if (!static::accessible($array)) {
            return static::value($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (!Strings::contains($key, '.')) {
            return static::value($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return static::value($default);
            }
        }

        return $array;
    }

    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int  $key
     * @return bool
     */
    public static function exists($array, $key)
    {
        if ($array instanceof \ArrayAccess) {
            return $array->offsetExists($key);
        }

        if (is_float($key)) {
            $key = (string) $key;
        }

        return array_key_exists($key, $array);
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param  \Traversable|array  $array
     * 
     * @return bool
     */
    public static function isIterable($array)
    {
        return is_array($array) || $array instanceof \Traversable;
    }

    /**
     * @param array &$array
     * @param string $key
     * @param mixed $default
     * 
     * @return mixed
     */
    public static function takeOffRecursive(&$array, $key, $default = null)
    {
        $keys = explode('.', $key);

        while (($currentKey = array_shift($keys)) !== null) {
            if ($currentKey === '*') {
                $values = array();

                foreach ($array as $subKey => &$subArray) {
                    if (empty($keys)) {
                        $values[] = $subArray;

                        unset($array[$subKey]);
                    } else {
                        $values[$subKey] = $subArray;
                    }
                }

                return $values;
            }

            if (array_key_exists($currentKey, $array)) {
                if (empty($keys)) {
                    $target = $array[$currentKey];

                    unset($array[$currentKey]);

                    return $target;
                } else {
                    $array = &$array[$currentKey];
                }
            } else {
                return $default;
            }
        }

        return $default;
    }

    /**
     * @param array<int|string, mixed> ...$array
     *
     * @return array<int|string, mixed>
     */
    public static function mergeDistinctRecursive()
    {
        $args = func_get_args();

        $merged = current($args);

        while (($current = current($args)) !== false) {
            $stack = array(
                array(&$merged, $current)
            );

            while (!empty($stack)) {
                $item = array_pop($stack);
                $target = &$item[0];
                $source = $item[1];

                foreach ($source as $key => $value) {
                    if (is_array($value) && isset($target[$key]) && is_array($target[$key])) {
                        $stack[] = array(&$target[$key], $value);
                    } else {
                        $target[$key] = $value;
                    }
                }
            }

            next($args);
        }

        return $merged;
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param  array  $array
     * @param  array  $sort
     * 
     * @return array
     */
    public static function fields($array, $sort)
    {
        $index = array();

        foreach ($sort as $i => $value) {
            if (!isset($index[$value])) {
                $index[$value] = $i;
            }
        }

        $buckets = array();
        $notFound = array();

        foreach ($array as $key => $value) {
            if (isset($index[$value])) {
                $buckets[$index[$value]][$key] = $value;
            } else {
                $notFound[$key] = $value;
            }
        }

        ksort($buckets);

        $result = array();

        foreach ($buckets as $bucket) {
            foreach ($bucket as $k => $v) {
                $result[$k] = $v;
            }
        }

        foreach ($notFound as $k => $v) {
            $result[$k] = $v;
        }

        return $result;
    }

    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @param  mixed  ...$args
     * @return mixed
     */
    public static function value()
    {
        $args = func_get_args();

        $value = array_shift($args);

        return $value instanceof \Closure ? call_user_func_array($value, $args) : $value;
    }

    /**
     * Collapse an array of arrays into a single array.
     *
     * @param  iterable  $array
     * @return array
     */
    public static function collapse($array)
    {
        $results = array();

        foreach ($array as $values) {
            if (!is_array($values)) {
                continue;
            }

            $results[] = $values;
        }

        array_unshift($results, array());

        return call_user_func_array('array_merge', $results);
    }

    /**
     * @param array $array
     * @param callback|\Closure $callback
     * @param mixed $initial
     *
     * @return mixed
     */
    public static function reduce($array, $callback, $initial = null)
    {
        return array_reduce($array, $callback, $initial);
    }

    /**
     * @param array $array
     * @param callable|null $callback
     *
     * @return mixed
     */
    public static function filter($array, $callback = null)
    {
        if (!$callback) {
            return array_filter($array);
        }

        if (!is_callable($callback)) {
            throw new \InvalidArgumentException("Argument 2 must be callback");
        }

        $newArray = array();

        foreach ($array as $key => $value) {
            $result = call_user_func($callback, $value, $key);

            if ($result) {
                $newArray[$key] = $value;
            }
        }

        return $newArray;
    }

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * 
     * @return void
     */
    public static function forget(&$array, $keys)
    {
        $original = &$array;

        $keys = (array) $keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (static::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            $parts = explode('.', $key);

            // clean up before each pass
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && static::accessible($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    /**
     * @param array $array
     * 
     * @return array
     */
    public static function keyCamel($array)
    {
        return array_combine(
            array_map(array('Wilkques\Helpers\Strings', 'camel'), array_keys($array)),
            $array
        );
    }

    /**
     * @param array $array
     * 
     * @return array
     */
    public static function keySnakeToCamel($array)
    {
        return static::keyCamel($array);
    }

    /**
     * @param array $array
     * 
     * @return array
     */
    public static function keyKebabCaseToCamel($array)
    {
        return static::keyCamel($array);
    }

    /**
     * Replace the array items with the given items.
     *
     * @param  array  ...$arrays
     * @return array
     */
    public static function replace()
    {
        $args = func_get_args();

        return call_user_func_array('array_replace', $args);
    }

    /**
     * Recursively replace the array items with the given items.
     *
     * @param  array  ...$arrays
     * @return array
     */
    public static function replaceRecursive()
    {
        $args = func_get_args();

        return call_user_func_array('array_replace_recursive', $args);
    }

    /**
     * Merge one or more arrays together.
     *
     * @param  array  ...$arrays
     * @return array
     */
    public static function merge()
    {
        $args = func_get_args();

        return call_user_func_array('array_merge', $args);
    }

    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param  iterable  $array
     * @param  callback|\Closure|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    public static function first($array, $callback = null, $default = null)
    {
        if (is_null($callback)) {
            if (empty($array)) {
                return static::value($default);
            }

            foreach ($array as $item) {
                return $item;
            }
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return static::value($default);
    }

    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array  $array
     * @param  callback|\Closure|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    public static function last($array, $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? static::value($default) : end($array);
        }

        return static::first(array_reverse($array, true), $callback, $default);
    }

    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @param  array  $array
     * @return array
     */
    public static function divide($array)
    {
        return array(array_keys($array), array_values($array));
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  iterable  $array
     * @param  string  $prepend
     * @return array
     */
    public static function dot($array, $prepend = '')
    {
        $results = array();

        static::dotInto($results, $array, $prepend);

        return $results;
    }

    /**
     * Flatten a multi-dimensional associative array with dots into the given results array by reference.
     *
     * @param  array  $results
     * @param  iterable  $array
     * @param  string  $prepend
     * @return void
     */
    protected static function dotInto(&$results, $array, $prepend = '')
    {
        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                static::dotInto($results, $value, $prepend . $key . '.');
            } else {
                $results[$prepend . $key] = $value;
            }
        }
    }

    /**
     * Convert a flatten "dot" notation array into an expanded array.
     *
     * @param  iterable  $array
     * @return array
     */
    public static function undot($array)
    {
        $results = array();

        foreach ($array as $key => $value) {
            static::set($results, $key, $value);
        }

        return $results;
    }

    /**
     * Check if an item or items exist in an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|array  $keys
     * @return bool
     */
    public static function has($array, $keys)
    {
        $keys = (array) $keys;

        if (!$array || $keys === array()) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (static::exists($array, $key)) {
                continue;
            }

            foreach (explode('.', $key) as $segment) {
                if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  iterable  $array
     * @param  int  $depth
     * @return array
     */
    public static function flatten($array, $depth = INF)
    {
        $result = array();

        foreach ($array as $item) {
            if (!is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1
                    ? array_values($item)
                    : static::flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Push an item onto the beginning of an array.
     *
     * @param  array  $array
     * @param  mixed  $value
     * @param  mixed  $key
     * @return array
     */
    public static function prepend($array, $value, $key = null)
    {
        if (func_num_args() == 2) {
            array_unshift($array, $value);
        } else {
            $array = array_replace(array($key => $value), $array);
        }

        return $array;
    }

    /**
     * Get a value from the array, and remove it.
     *
     * @param  array  $array
     * @param  string|int  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function pull(&$array, $key, $default = null)
    {
        $value = static::get($array, $key, $default);

        static::forget($array, $key);

        return $value;
    }

    /**
     * If the given value is not an array and not null, wrap it in one.
     *
     * @param  mixed  $value
     * @return array
     */
    public static function wrap($value)
    {
        if (is_null($value)) {
            return array();
        }

        return is_array($value) ? $value : array($value);
    }

    /**
     * @param array $array
     * @param int|string $columnKey
     * @param int|string|null $indexKey
     * 
     * @return array
     */
    public static function column($array, $columnKey, $indexKey = null)
    {
        $result = array();

        foreach ($array as $row) {
            $key = $value = null;

            $value = Objects::get($row, $columnKey);

            if ($indexKey !== null) {
                $key = Objects::get($row, $indexKey);

                static::set($result, $key, $value);
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * Determine if an array is a list (sequential, zero-based integer keys).
     *
     * @param  array  $array
     * @return bool
     */
    public static function isList($array)
    {
        return array_values($array) === $array;
    }

    /**
     * Determine if an array is associative (not a list).
     *
     * @param  array  $array
     * @return bool
     */
    public static function isAssoc($array)
    {
        return !static::isList($array);
    }

    /**
     * Get one or a specified number of random values from an array.
     *
     * @param  array  $array
     * @param  int|null  $number
     * @param  bool  $preserveKeys
     * @return mixed
     */
    public static function random($array, $number = null, $preserveKeys = false)
    {
        $requested = is_null($number) ? 1 : $number;

        $count = count($array);

        if ($requested > $count) {
            throw new \InvalidArgumentException(
                "You requested {$requested} items, but there are only {$count} items available."
            );
        }

        if (empty($array) || (!is_null($number) && $number <= 0)) {
            return is_null($number) ? null : array();
        }

        if (is_null($number)) {
            return $array[array_rand($array)];
        }

        $keys = (array) array_rand($array, $number);

        $results = array();

        foreach ($keys as $key) {
            if ($preserveKeys) {
                $results[$key] = $array[$key];
            } else {
                $results[] = $array[$key];
            }
        }

        return $results;
    }

    /**
     * Shuffle the given array and return the result.
     *
     * @param  array  $array
     * @param  int|null  $seed
     * @return array
     */
    public static function shuffle($array, $seed = null)
    {
        if (is_null($seed)) {
            shuffle($array);
        } else {
            mt_srand($seed);
            shuffle($array);
            mt_srand();
        }

        return $array;
    }

    /**
     * Sort the array using the given callback or "dot" notation key.
     *
     * @param  array  $array
     * @param  callable|string|null  $callback
     * @return array
     */
    public static function sort($array, $callback = null)
    {
        if (is_null($callback)) {
            $items = $array;

            asort($items);

            return $items;
        }

        $results = array();

        foreach ($array as $key => $value) {
            $results[$key] = is_callable($callback) ? call_user_func($callback, $value) : Objects::get($value, $callback);
        }

        asort($results);

        $items = array();

        foreach (array_keys($results) as $key) {
            $items[$key] = $array[$key];
        }

        return $items;
    }

    /**
     * Sort the array in descending order using the given callback or "dot" notation key.
     *
     * @param  array  $array
     * @param  callable|string|null  $callback
     * @return array
     */
    public static function sortDesc($array, $callback = null)
    {
        if (is_null($callback)) {
            $items = $array;

            arsort($items);

            return $items;
        }

        return array_reverse(static::sort($array, $callback), true);
    }

    /**
     * Recursively sort an array by keys and values.
     *
     * @param  array  $array
     * @param  int  $options
     * @param  bool  $descending
     * @return array
     */
    public static function sortRecursive($array, $options = SORT_REGULAR, $descending = false)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = static::sortRecursive($value, $options, $descending);
            }
        }

        if (static::isList($array)) {
            $descending ? rsort($array, $options) : sort($array, $options);
        } else {
            $descending ? krsort($array, $options) : ksort($array, $options);
        }

        return $array;
    }

    /**
     * Build a query string from the given array.
     *
     * @param  array  $array
     * @return string
     */
    public static function query($array)
    {
        // PHP_QUERY_RFC3986 (and the 4th http_build_query() argument that
        // takes it) only exist from PHP 5.4 — calling with a 4th argument
        // on 5.3 raises a "wrong parameter count" warning. Falls back to
        // the 3-arg RFC1738 default (spaces encoded as "+") there.
        if (defined('PHP_QUERY_RFC3986')) {
            return http_build_query($array, '', '&', PHP_QUERY_RFC3986);
        }

        return http_build_query($array, '', '&');
    }

    /**
     * Cross join the given arrays, returning all possible permutations.
     *
     * @param  array  ...$arrays
     * @return array
     */
    public static function crossJoin()
    {
        $arrays = func_get_args();

        $results = array(array());

        foreach ($arrays as $index => $array) {
            $append = array();

            foreach ($results as $product) {
                foreach ($array as $item) {
                    $product[$index] = $item;

                    $append[] = $product;
                }
            }

            $results = $append;
        }

        return $results;
    }

    /**
     * Partition the array into two arrays using the given callback.
     *
     * @param  array  $array
     * @param  callable  $callback
     * @return array
     */
    public static function partition($array, $callback)
    {
        $passed = array();

        $failed = array();

        foreach ($array as $key => $item) {
            if (call_user_func($callback, $item, $key)) {
                $passed[$key] = $item;
            } else {
                $failed[$key] = $item;
            }
        }

        return array($passed, $failed);
    }

    /**
     * Join all items using a string, with a final glue for the last item.
     *
     * @param  array  $array
     * @param  string  $glue
     * @param  string  $finalGlue
     * @return string
     */
    public static function join($array, $glue, $finalGlue = '')
    {
        if ($finalGlue === '') {
            return implode($glue, $array);
        }

        $count = count($array);

        if ($count === 0) {
            return '';
        }

        if ($count === 1) {
            return end($array);
        }

        $finalItem = array_pop($array);

        return implode($glue, $array) . $finalGlue . $finalItem;
    }

    /**
     * Take the first or last {$limit} items from an array.
     *
     * @param  array  $array
     * @param  int  $limit
     * @return array
     */
    public static function take($array, $limit)
    {
        if ($limit < 0) {
            return array_slice($array, $limit, abs($limit));
        }

        return array_slice($array, 0, $limit);
    }

    /**
     * Filter items where the value is not null.
     *
     * @param  array  $array
     * @return array
     */
    public static function whereNotNull($array)
    {
        return static::where($array, function ($value) {
            return !is_null($value);
        });
    }
}
