<?php

if (!function_exists('string_snake')) {
    /**
     * @param string $camelCase
     * 
     * @return array|string|null
     */
    function string_snake($camelCase)
    {
        return preg_replace_callback(
            ["/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"],
            function ($matches) {
                return "_" . lcfirst($matches[0]);
            },
            $camelCase
        );
    }
}

if (!function_exists('array_key_sanke')) {
    /**
     * String snake to study
     * 
     * @param array $array
     * 
     * @return array
     */
    function array_key_sanke($array)
    {
        return array_combine(string_snake(array_keys($array)), $array);
    }
}

if (!function_exists('data_set')) {
    /**
     * Set an item on an array or object using dot notation.
     *
     * @param  mixed  $target
     * @param  string|array  $key
     * @param  mixed  $value
     * @param  bool  $overwrite
     * @return mixed
     */
    function data_set(&$target, $key, $value, $overwrite = true)
    {
        $segments = is_array($key) ? $key : explode('.', $key);

        if (($segment = array_shift($segments)) === '*') {
            if (!accessible($target)) {
                $target = [];
            }

            if ($segments) {
                foreach ($target as &$inner) {
                    data_set($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (accessible($target)) {
            if ($segments) {
                if (!exists($target, $segment)) {
                    $target[$segment] = [];
                }

                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || !exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (!isset($target->{$segment})) {
                    $target->{$segment} = [];
                }

                data_set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || !isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];

            if ($segments) {
                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }

        return $target;
    }
}

if (!function_exists('accessible')) {
    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    function accessible($value)
    {
        return is_array($value) || $value instanceof \ArrayAccess;
    }
}

if (!function_exists('exists')) {
    /**
     * Determine if the given key exists in the provided array.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int  $key
     * @return bool
     */
    function exists($array, $key)
    {
        if ($array instanceof \ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }
}

if (!function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed  $target
     * @param  string|array|int|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        foreach ($key as $i => $segment) {
            unset($key[$i]);

            if (is_null($segment)) {
                return $target;
            }

            if ($segment === '*') {
                if (!is_iterable($target)) {
                    return value($default);
                }

                $result = [];

                foreach ($target as $item) {
                    $result[] = data_get($item, $key);
                }

                return $result;
            }

            if (is_array($target) && array_key_exists($segment, $target)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }
}

if (!function_exists('is_iterable')) {
    function is_iterable($obj)
    {
        return is_array($obj) || $obj instanceof \Traversable;
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @param  mixed  ...$args
     * @return mixed
     */
    function value()
    {
        $args = func_get_args();

        $value = array_shift($args);

        return $value instanceof \Closure ? call_user_func_array($value, $args) : $value;
    }
}

if (!function_exists('array_take_off_recursive')) {
    /**
     * @param array &$array
     * @param string $key
     * @param mixed $default
     * 
     * @return mixed
     */
    function array_take_off_recursive(&$array, $key, $default = null)
    {
        $keys = explode('.', $key);
        $currentKey = array_shift($keys);

        if ($currentKey === '*') {
            $values = array();

            foreach ($array as $subKey => &$subArray) {
                if (empty($keys)) {
                    $values[] = $subArray;
                    unset($array[$subKey]);
                } else {
                    $values[$subKey] = array_take_off_recursive($subArray, implode('.', $keys));
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
                return array_take_off_recursive($array[$currentKey], implode('.', $keys));
            }
        }

        return $default;
    }
}

if (!function_exists('array_merge_recursive_distinct')) {

    /**
     * @param array<int|string, mixed> $array1
     * @param array<int|string, mixed> $array2
     *
     * @return array<int|string, mixed>
     */
    function array_merge_recursive_distinct(array &$array1, array &$array2): array
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = array_merge_recursive_distinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}

if (!function_exists('dir_scan')) {
    /**
     * @param string $dir
     * 
     * @return \Generator<string>
     */
    function dir_scan($dir)
    {
        foreach (scandir($dir) as $path) {
            if (!in_array($path, array(".", ".."))) {
                $findPath = $dir . DIRECTORY_SEPARATOR;
                if (is_dir($findPath . $path)) {
                    dir_scan($findPath . $path);
                } else {
                    yield $findPath . $path;
                }
            }
        }
    }
}