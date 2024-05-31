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
            array_map(array(Strings::class, 'snake'), array_keys($array)), 
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
        return array_map($callback, $array, array_keys($array));
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

        if (count($array) > 0) {
            foreach ($array as $item) {
                $itemValue = $item[$value];

                if (is_null($key)) {
                    $results[] = $itemValue;
                } else {
                    $itemKey = $item[$key];

                    if (is_string($case)) {
                        $case = Strings::lower($case);
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
        return Arrays::filter($array, $callback);
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
        if (!static::isAccessible($array)) {
            return static::value($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (!Strings::contains($key, '.')) {
            return $array[$key] ?? static::value($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (static::isAccessible($array) && static::exists($array, $segment)) {
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
    public static function isAccessible($value)
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
        uasort($array, function ($a, $b) use ($sort) {
            return array_search($a, $sort) <=> array_search($b, $sort);
        });

        return $array;
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
     * 
     * @return mixed
     */
    public static function reduce($array, $callback)
    {
        return array_reduce($array, $callback);
    }

    /**
     * @param array $array
     * @param callback|\Closure $callback
     * 
     * @return mixed
     */
    public static function filter($array, $callback)
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
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

                if (isset($array[$part]) && is_array($array[$part])) {
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
            array_map(array(Strings::class, 'camel'), array_keys($array)), 
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
}
