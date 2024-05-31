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
     * @param string $camelCase
     * 
     * @return array|string|null
     */
    public static function snake($camelCase)
    {
        return preg_replace_callback(
            array("/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"),
            function ($matches) {
                return "_" . lcfirst($matches[0]);
            },
            $camelCase
        );
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
     * @param  string|array  $delimiter
     * @param  int  $case
     * 
     * @return string
     */
    public static function delimiterReplace($value, $delimiter = '_', $case = MB_CASE_LOWER)
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = str_convert_case(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value), $case);
        }

        return $value;
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
}
