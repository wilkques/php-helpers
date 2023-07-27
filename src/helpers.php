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
            array("/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"),
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

if (!function_exists("array_only")) {
    /**
     * Get a subset of the items from the given array.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * 
     * @return array
     */
    function array_only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
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
                $target = array();
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
                    $target[$segment] = array();
                }

                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || !exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (!isset($target->{$segment})) {
                    $target->{$segment} = array();
                }

                data_set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || !isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = array();

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

                $result = array();

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
}

if (!function_exists('array_merge_distinct_recursive')) {

    /**
     * @param array<int|string, mixed> ...$array
     *
     * @return array<int|string, mixed>
     */
    function array_merge_distinct_recursive()
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
}

if (!function_exists('dir_scan')) {
    /**
     * @param string $dir
     * 
     * @return \Generator<string>
     */
    function dir_scan($dir)
    {
        $stack = new SplStack();

        $stack->push($dir);

        while (!$stack->isEmpty()) {
            $currentDir = $stack->pop();

            foreach (scandir($currentDir) as $path) {
                if (!in_array($path, array(".", ".."))) {
                    $findPath = $currentDir . DIRECTORY_SEPARATOR . $path;

                    if (is_dir($findPath)) {
                        $stack->push($findPath);
                    } else {
                        yield $findPath;
                    }
                }
            }
        }
    }
}

if (!function_exists('str_starts_with')) {
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    function str_starts_with($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) === 0) return true;
        }

        return false;
    }
}

if (!function_exists('str_ends_with')) {
    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    function str_ends_with($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle === substr($haystack, -strlen($needle))) return true;
        }

        return false;
    }
}

if (!function_exists("ve")) {
    function ve()
    {
        $args = func_get_args();

        array_walk($args, function ($arg) {
            var_export($arg);

            echo PHP_EOL;
        });
    }
}

if (!function_exists("ved")) {
    function ved()
    {
        $args = func_get_args();

        call_user_func('ve', $args);

        die;
    }
}

if (!function_exists('json_error_check')) {
    /**
     * @return array
     */
    function json_error_check()
    {
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $message = ' - No errors';
                break;
            case JSON_ERROR_DEPTH:
                $message = ' - Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $message = ' - Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $message = ' - Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $message = ' - Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $message = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $message = ' - Unknown error';
                break;
        }

        return [
            'code'      => json_last_error(),
            'message'   => $message
        ];
    }
}
