<?php

use Wilkques\Helpers\Arrays;
use Wilkques\Helpers\Objects;
use Wilkques\Helpers\Strings;

if (!function_exists('str_snake')) {
    /**
     * @param string $camelCase
     * 
     * @return array|string|null
     */
    function string_snake($camelCase)
    {
        return Strings::snake($camelCase);
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
        return Arrays::keySnake($array);
    }
}

if (!function_exists('array_pluck')) {
    /**
     * @param array|object $array
     * @param string $value
     * @param string|null $key
     * @param int|null $case Either CASE_UPPER or CASE_LOWER or null
     * 
     * @return array
     */
    function array_pluck($array, string $value, string $key = null, int $case = null)
    {
       return Arrays::pluck($array, $value, $key, $case);
    }
}

if (!function_exists('array_map_with_keys')) {
    /**
     * @param array $array
     * @param callable $callback
     * 
     * @return array
     */
    function array_map_with_keys($array, callable $callback)
    {
        return Arrays::mapWithKeys($array, $callback);
    }
}

if (!function_exists('array_where')) {
    /**
     * @param array $array
     * @param callable $callback
     * 
     * @return array
     */
    function array_where($array, callable $callback)
    {
        return Arrays::where($array, $callback);
    }
}

if (!function_exists('data_where')) {
    /**
     * @param array $array
     * @param callable $callback
     * 
     * @return array
     */
    function data_where($array, callable $callback)
    {
        return Arrays::where($array, $callback);
    }
}

if (!function_exists("array_only")) {
    /**
     * 
     *
     * @param  array  $array
     * @param  array|string  $keys
     * 
     * @return array
     */
    function array_only($array, $keys)
    {
        return Arrays::only($array, $keys);
    }
}

if (!function_exists("array_except")) {
    /**
     *
     *
     * @param  array  $array
     * @param  array|string  $keys
     * 
     * @return array
     */
    function array_except($array, $keys)
    {
        return Arrays::except($array, $keys);
    }
}

if (!function_exists("array_key_fields")) {
    /**
     * @param array $array
     * @param array $sort
     * 
     * @return array
     */
    function array_key_fields($array, $sort)
    {
        return Arrays::keyFields($array, $sort);
    }
}

if (!function_exists("array_fields")) {
    /**
     * @param array $array
     * @param array $sort
     * 
     * @return array
     */
    function array_fields($array, $sort)
    {
        return Arrays::fields($array, $sort);
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
        return Objects::set($target, $key, $value, $overwrite);
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
        return Arrays::exists($array, $key);
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
        return Objects::get($target, $key, $default);
    }
}

if (!function_exists('is_iterable')) {
    /**
     * Determine if the given key exists in the provided array.
     *
     * @param  \Traversable|array  $array
     * 
     * @return bool
     */
    function is_iterable($obj)
    {
        return Arrays::isIterable($obj);
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
        return call_user_func_array(array('Wilkques\Helpers\Arrays', 'value'), func_get_args());
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
        return Arrays::takeOffRecursive($array, $key, $default);
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
        return call_user_func_array(array('Wilkques\Helpers\Arrays', 'mergeDistinctRecursive'), func_get_args());
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
        return Strings::startsWith($haystack, $needles);
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
        return Strings::endsWith($haystack, $needles);
    }
}

if (!function_exists('str_contains')) {
    /**
     * @param string $haystack
     * @param array|string $needles
     * 
     * @return bool
     */
    function str_contains($haystack, $needles)
    {
        return Strings::contains($haystack, $needles);
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

        call_user_func_array('ve', $args);

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

if (!function_exists("array_field")) {
    /**
     * Get a subset of the items from the given array.
     *
     * @param  array  $array
     * @param  array  $keys
     * 
     * @return array
     */
    function array_field($array, $keys)
    {
        return Arrays::fields($array, $keys);
    }
}

if (!function_exists("array_set")) {
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
    function array_set(&$array, $key, $value)
    {
        return Arrays::set($array, $key, $value);
    }
}

if (!function_exists("array_get")) {
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        return Arrays::get($array, $key, $default);
    }
}

if (!function_exists("is_accessible")) {
    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    function is_accessible($value)
    {
        return Arrays::isAccessible($value);
    }
}

if (!function_exists("str_delimiter_replace")) {
    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $delimiter
     * 
     * @return string
     */
    function str_delimiter_replace($value, $delimiter = '_')
    {
        return Strings::delimiterReplace($value, $delimiter);
    }
}

if (!function_exists("str_convert_case")) {
    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string  $haystack
     * @param  int  $case
     * 
     * @return string
     */
    function str_convert_case($value, $case = MB_CASE_LOWER)
    {
        return Strings::convertCase($value, $case);
    }
}

if (!function_exists("array_forget")) {
    /**
     * Get a subset of the items from the given array.
     *
     * @param  array  $array
     * @param  array|string  ...$keys
     * 
     * @return void
     */
    function array_forget(&$array, $keys)
    {
        Arrays::forget($array, $keys);
    }
}

if (!function_exists("array_key_kebab_case_to_camel")) {
    /**
     * @param  array  $array
     * 
     * @return array
     */
    function array_key_kebab_case_to_camel($array)
    {
        return Arrays::keyKebabCaseToCamel($array);
    }
}

if (!function_exists("str_kebab_case_to_camel")) {
    /**
     * @param  string  $string
     * 
     * @return string
     */
    function str_kebab_case_to_camel($string)
    {
        return Strings::kebabCaseToCamel($string);
    }
}

if (!function_exists("array_key_snake_to_camel")) {
    /**
     * @param  array  $array
     * 
     * @return array
     */
    function array_key_snake_to_camel($array)
    {
        return Arrays::keySnakeToCamel($array);
    }
}

if (!function_exists("str_snake_to_camel")) {
    /**
     * @param  string  $string
     * 
     * @return string
     */
    function str_snake_to_camel($string)
    {
        return Strings::snakeToCamel($string);
    }
}

if (!function_exists("str_camel")) {
    /**
     * replace snake & kebab case to camel
     * 
     * @param  string  $string
     * 
     * @return string
     */
    function str_camel($string)
    {
        return Strings::camel($string);
    }
}
