<?php

namespace Wilkques\Helpers;

class Objects
{
    /**
     * Set an item on an array or object using dot notation.
     *
     * @param  mixed  $target
     * @param  string|array  $key
     * @param  mixed  $value
     * @param  bool  $overwrite
     * @return mixed
     */
    public static function set(&$target, $key, $value, $overwrite = true)
    {
        $segments = is_array($key) ? $key : explode('.', $key);

        if (($segment = array_shift($segments)) === '*') {
            if (!static::accessible($target)) {
                $target = array();
            }

            if ($segments) {
                foreach ($target as &$inner) {
                    static::set($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (static::accessible($target)) {
            if ($segments) {
                if (!static::exists($target, $segment)) {
                    $target[$segment] = array();
                }

                static::set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || !static::exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (!isset($target->{$segment})) {
                    $target->{$segment} = array();
                }

                static::set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || !isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = array();

            if ($segments) {
                static::set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }

        return $target;
    }

    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed  $target
     * @param  string|array|int|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get($target, $key, $default = null)
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
                if (!Arrays::isIterable($target)) {
                    return static::value($default);
                }

                $result = array();

                foreach ($target as $item) {
                    $result[] = static::get($item, $key);
                }

                return in_array('*', $key) ? Arrays::collapse($result) : $result;
            }

            // Every {first}/{last}/escaped form starts with '\' or '{'; this
            // single cheap check skips the 5-way comparison below for the
            // overwhelmingly common case of an ordinary segment.
            $firstChar = substr($segment, 0, 1);

            if ($firstChar === '\\' || $firstChar === '{') {
                if ($segment === '\*') {
                    $segment = '*';
                } elseif ($segment === '\{first}') {
                    $segment = '{first}';
                } elseif ($segment === '{first}') {
                    $segment = static::arrayKeyFirst(static::normalizeForKeyLookup($target));
                } elseif ($segment === '\{last}') {
                    $segment = '{last}';
                } elseif ($segment === '{last}') {
                    $segment = static::arrayKeyLast(static::normalizeForKeyLookup($target));
                }
            }

            if (static::accessible($target) && Arrays::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return static::value($default);
            }
        }

        return $target;
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
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function accessible($value)
    {
        return is_array($value) || $value instanceof \ArrayAccess;
    }

    /**
     * Normalize a get() target into a plain array so its first/last key
     * can be read, without depending on Collections (which itself depends
     * on this class).
     *
     * @param  mixed  $target
     * @return array
     */
    protected static function normalizeForKeyLookup($target)
    {
        if (is_array($target)) {
            return $target;
        }

        if ($target instanceof \Traversable) {
            return iterator_to_array($target);
        }

        return (array) $target;
    }

    /**
     * PHP 5.3-safe replacement for array_key_first() (PHP 7.3+). The
     * $array parameter is passed by value, so moving its internal pointer
     * here has no effect on the caller's copy.
     *
     * @param  array  $array
     * @return int|string|null
     */
    protected static function arrayKeyFirst($array)
    {
        reset($array);

        return key($array);
    }

    /**
     * PHP 5.3-safe replacement for array_key_last() (PHP 7.3+).
     *
     * @param  array  $array
     * @return int|string|null
     */
    protected static function arrayKeyLast($array)
    {
        end($array);

        return key($array);
    }
}
