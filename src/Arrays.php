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
            if (static::exists($array, $key)) {
                return static::get($array, $key);
            }

            return  static::value($default);
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
            $indexA = array_search($a, $sort);

            $indexB = array_search($b, $sort);

            if ($indexA === false && $indexB === false) {
                return 0; // 如果兩個元素都不在排序陣列中，則視為相等
            } elseif ($indexA === false) {
                return 1; // 如果 $a 不在排序陣列中，則視 $b 為更小的元素
            } elseif ($indexB === false) {
                return -1; // 如果 $b 不在排序陣列中，則視 $a 為更小的元素
            }

            // 如果兩個元素都在排序陣列中，則比較它們在排序陣列中的索引位置
            return $indexA < $indexB ? -1 : ($indexA > $indexB ? 1 : 0);
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
        // return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);

        $newArray = array();

        foreach ($array as $key => $value) {
            $result = $callback($value, $key);

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
     * Replace the collection items with the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  ...$replacements
     * @return array
     */
    public static function replace()
    {
        $args = func_get_args();

        return call_user_func_array('array_replace', $args);
    }

    /**
     * Recursively replace the collection items with the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  ...$replacements
     * @return array
     */
    public static function replaceRecursive()
    {
        $args = func_get_args();

        return call_user_func_array('array_replace_recursive', $args);
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

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, static::dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
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
}
