<?php

namespace Wilkques\Helpers;

class Strings
{
    /**
     * @param string $haystack
     * @param array|string $needles
     * 
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        if (!is_array($needles)) {
            $needles = array(
                $needles,
            );
        }

        foreach ($needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $string
     * 
     * @return array|string|null
     */
    public static function snake($string)
    {
        return static::delimiterReplace($string, '_');
    }

    /**
     * Convert a string to kebab case.
     *
     * @param  string  $string
     * @return string
     */
    public static function kebab($string)
    {
        return static::delimiterReplace($string, '-');
    }

    /**
     * @param  string  $string
     * 
     * @return string
     */
    public static function camel($string)
    {
        return preg_replace_callback('/[-_](\w)/i', function ($match) {
            return strtoupper($match[1]);
        }, $string);
    }

    /**
     * Convert the given string to lower-case.
     *
     * @param  string  $value
     * @return string
     */
    public static function lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Convert the given string to lower-case.
     *
     * @param  string  $value
     * @return string
     */
    public static function upper($value)
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && strpos($haystack, $needle) === 0) return true;
        }

        return false;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    public static function endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle === substr($haystack, -strlen($needle))) return true;
        }

        return false;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string  $haystack
     * @param  string  $delimiter
     * 
     * @return string
     */
    public static function delimiterReplace($haystack, $delimiter = '_')
    {
        $string = preg_replace('/\s+/u', '', ucwords($haystack));

        return static::lower(
            preg_replace_callback('/(.)(?=[A-Z])/u', function ($match) use ($delimiter) {
                return $match[0] . $delimiter;
            }, $string)
        );
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string  $haystack
     * @param  int  $case
     * 
     * @return string
     */
    public static function convertCase($value, $case = MB_CASE_LOWER)
    {
        return mb_convert_case($value, $case, 'UTF-8');
    }

    /**
     * @param  string  $string
     * 
     * @return string
     */
    public static function kebabCaseToCamel($string)
    {
        return preg_replace_callback('/-(\w)/i', function ($match) {
            return strtoupper($match[1]);
        }, $string);
    }

    /**
     * @param  string  $string
     * 
     * @return string
     */
    public static function snakeToCamel($string)
    {
        return preg_replace_callback('/_(\w)/i', function ($match) {
            return strtoupper($match[1]);
        }, $string);
    }
}
