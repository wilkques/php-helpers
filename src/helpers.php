<?php

use Wilkques\Helpers\Arrays;
use Wilkques\Helpers\Collections;
use Wilkques\Helpers\Objects;
use Wilkques\Helpers\Strings;

if (!function_exists('str_snake')) {
    /**
     * @param string $camelCase
     *
     * @return array|string|null
     */
    function str_snake($camelCase)
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
    function array_pluck($array, $value, $key = null, $case = null)
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

        return array(
            'code'      => json_last_error(),
            'message'   => $message,
        );
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

if (!function_exists("array_has")) {
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int|null  $key
     * @param  mixed  $default
     * @return bool
     */
    function array_has($array, $key)
    {
        return Arrays::has($array, $key);
    }
}

if (!function_exists("accessible")) {
    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    function accessible($value)
    {
        return Arrays::accessible($value);
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

if (!function_exists("array_column")) {
    /**
     * @param  array  $array
     * @param int|string $columnKey
     * @param int|string|null $indexKey
     * 
     * @return array
     */
    function array_column($array, $columnKey, $indexKey = null)
    {
        return Arrays::column($array, $columnKey, $indexKey);
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

if (!function_exists("array_is_list")) {
    /**
     * Determine if an array is a list (sequential, zero-based integer keys).
     *
     * @param  array  $array
     * @return bool
     */
    function array_is_list($array)
    {
        return Arrays::isList($array);
    }
}

if (!function_exists("array_is_assoc")) {
    /**
     * Determine if an array is associative (not a list).
     *
     * @param  array  $array
     * @return bool
     */
    function array_is_assoc($array)
    {
        return Arrays::isAssoc($array);
    }
}

if (!function_exists("array_random")) {
    /**
     * Get one or a specified number of random values from an array.
     *
     * @param  array  $array
     * @param  int|null  $number
     * @param  bool  $preserveKeys
     * @return mixed
     */
    function array_random($array, $number = null, $preserveKeys = false)
    {
        return Arrays::random($array, $number, $preserveKeys);
    }
}

if (!function_exists("array_shuffle")) {
    /**
     * Shuffle the given array and return the result.
     *
     * @param  array  $array
     * @param  int|null  $seed
     * @return array
     */
    function array_shuffle($array, $seed = null)
    {
        return Arrays::shuffle($array, $seed);
    }
}

if (!function_exists("array_sort")) {
    /**
     * Sort the array using the given callback or "dot" notation key.
     *
     * @param  array  $array
     * @param  callable|string|null  $callback
     * @return array
     */
    function array_sort($array, $callback = null)
    {
        return Arrays::sort($array, $callback);
    }
}

if (!function_exists("array_sort_desc")) {
    /**
     * Sort the array in descending order using the given callback or "dot" notation key.
     *
     * @param  array  $array
     * @param  callable|string|null  $callback
     * @return array
     */
    function array_sort_desc($array, $callback = null)
    {
        return Arrays::sortDesc($array, $callback);
    }
}

if (!function_exists("array_sort_recursive")) {
    /**
     * Recursively sort an array by keys and values.
     *
     * @param  array  $array
     * @param  int  $options
     * @param  bool  $descending
     * @return array
     */
    function array_sort_recursive($array, $options = SORT_REGULAR, $descending = false)
    {
        return Arrays::sortRecursive($array, $options, $descending);
    }
}

if (!function_exists("array_query")) {
    /**
     * Build a query string from the given array.
     *
     * @param  array  $array
     * @return string
     */
    function array_query($array)
    {
        return Arrays::query($array);
    }
}

if (!function_exists("array_cross_join")) {
    /**
     * Cross join the given arrays, returning all possible permutations.
     *
     * @param  array  ...$arrays
     * @return array
     */
    function array_cross_join()
    {
        return call_user_func_array(array('Wilkques\Helpers\Arrays', 'crossJoin'), func_get_args());
    }
}

if (!function_exists("array_partition")) {
    /**
     * Partition the array into two arrays using the given callback.
     *
     * @param  array  $array
     * @param  callable  $callback
     * @return array
     */
    function array_partition($array, $callback)
    {
        return Arrays::partition($array, $callback);
    }
}

if (!function_exists("array_join")) {
    /**
     * Join all items using a string, with a final glue for the last item.
     *
     * @param  array  $array
     * @param  string  $glue
     * @param  string  $finalGlue
     * @return string
     */
    function array_join($array, $glue, $finalGlue = '')
    {
        return Arrays::join($array, $glue, $finalGlue);
    }
}

if (!function_exists("array_take")) {
    /**
     * Take the first or last {$limit} items from an array.
     *
     * @param  array  $array
     * @param  int  $limit
     * @return array
     */
    function array_take($array, $limit)
    {
        return Arrays::take($array, $limit);
    }
}

if (!function_exists("array_where_not_null")) {
    /**
     * Filter items where the value is not null.
     *
     * @param  array  $array
     * @return array
     */
    function array_where_not_null($array)
    {
        return Arrays::whereNotNull($array);
    }
}

if (!function_exists("str_random")) {
    /**
     * Generate a random string of the given length.
     *
     * @param  int  $length
     * @return string
     */
    function str_random($length)
    {
        return Strings::rand($length);
    }
}

if (!function_exists("class_basename")) {
    /**
     * Get the class "basename" of the given object / class.
     *
     * @param  string|object  $class
     * @return string
     */
    function class_basename($class)
    {
        return Strings::getClassBaseName($class);
    }
}

if (!function_exists("str_ucfirst")) {
    /**
     * Make a string's first character uppercase (multibyte-safe).
     *
     * @param  string  $string
     * @return string
     */
    function str_ucfirst($string)
    {
        return Strings::ucfirst($string);
    }
}

if (!function_exists("str_studly")) {
    /**
     * Convert a value to studly caps case (PascalCase).
     *
     * @param  string  $string
     * @return string
     */
    function str_studly($string)
    {
        return Strings::studly($string);
    }
}

if (!function_exists("str_limit")) {
    /**
     * Limit the number of characters in a string.
     *
     * @param  string  $value
     * @param  int  $limit
     * @param  string  $end
     * @return string
     */
    function str_limit($value, $limit = 100, $end = '...')
    {
        return Strings::limit($value, $limit, $end);
    }
}

if (!function_exists("str_slug")) {
    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param  string  $title
     * @param  string  $separator
     * @return string
     */
    function str_slug($title, $separator = '-')
    {
        return Strings::slug($title, $separator);
    }
}

if (!function_exists("str_mask")) {
    /**
     * Masks a portion of a string with a repeated character.
     *
     * @param  string  $string
     * @param  string  $character
     * @param  int  $index
     * @param  int|null  $length
     * @return string
     */
    function str_mask($string, $character, $index, $length = null)
    {
        return Strings::mask($string, $character, $index, $length);
    }
}

if (!function_exists("str_pad_left")) {
    /**
     * Pad the left side of a string with another.
     *
     * @param  string  $value
     * @param  int  $length
     * @param  string  $pad
     * @return string
     */
    function str_pad_left($value, $length, $pad = ' ')
    {
        return Strings::padLeft($value, $length, $pad);
    }
}

if (!function_exists("str_pad_right")) {
    /**
     * Pad the right side of a string with another.
     *
     * @param  string  $value
     * @param  int  $length
     * @param  string  $pad
     * @return string
     */
    function str_pad_right($value, $length, $pad = ' ')
    {
        return Strings::padRight($value, $length, $pad);
    }
}

if (!function_exists("str_pad_both")) {
    /**
     * Pad both sides of a string with another.
     *
     * @param  string  $value
     * @param  int  $length
     * @param  string  $pad
     * @return string
     */
    function str_pad_both($value, $length, $pad = ' ')
    {
        return Strings::padBoth($value, $length, $pad);
    }
}

if (!function_exists("str_headline")) {
    /**
     * Convert the given string to title case for each word, separated by spaces.
     *
     * @param  string  $value
     * @return string
     */
    function str_headline($value)
    {
        return Strings::headline($value);
    }
}

if (!function_exists("str_is_uuid")) {
    /**
     * Determine if a given value is a valid UUID.
     *
     * @param  mixed  $value
     * @return bool
     */
    function str_is_uuid($value)
    {
        return Strings::isUuid($value);
    }
}

if (!function_exists("str_is_ulid")) {
    /**
     * Determine if a given value is a valid ULID.
     *
     * @param  mixed  $value
     * @return bool
     */
    function str_is_ulid($value)
    {
        return Strings::isUlid($value);
    }
}

if (!function_exists("str_squish")) {
    /**
     * Remove all "extra" blank space from the given string.
     *
     * @param  string  $value
     * @return string
     */
    function str_squish($value)
    {
        return Strings::squish($value);
    }
}

if (!function_exists("str_plural")) {
    /**
     * Get the plural form of an English word.
     *
     * @param  string  $value
     * @param  int  $count
     * @return string
     */
    function str_plural($value, $count = 2)
    {
        return Strings::plural($value, $count);
    }
}

if (!function_exists("str_singular")) {
    /**
     * Get the singular form of an English word.
     *
     * @param  string  $value
     * @return string
     */
    function str_singular($value)
    {
        return Strings::singular($value);
    }
}

if (!function_exists("collect")) {
    /**
     * Create a collection from the given value.
     *
     * @param  mixed  $items
     * @return \Wilkques\Helpers\Collections
     */
    function collect($items = array())
    {
        return new Collections($items);
    }
}
