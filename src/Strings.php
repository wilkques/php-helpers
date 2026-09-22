<?php

namespace Wilkques\Helpers;

class Strings
{
    /**
     * Common English irregular plural forms.
     *
     * Note: this is a small, hand-picked rule set for common cases only,
     * it is not a full inflector like Laravel's (which relies on doctrine/inflector).
     *
     * @var array
     */
    protected static $irregular = array(
        'child'  => 'children',
        'person' => 'people',
        'man'    => 'men',
        'woman'  => 'women',
        'tooth'  => 'teeth',
        'foot'   => 'feet',
        'mouse'  => 'mice',
        'goose'  => 'geese',
    );

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

    /**
     * @param  int  $length
     * 
     * @return string
     */
    public static function rand($length)
    {
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $len = strlen($str) - 1;

        $randstr = '';

        for ($i = 0; $i < $length; $i++) {
            $num = mt_rand(0, $len);

            $randstr .= $str[$num];
        }

        return $randstr;
    }

    /**
     * Get the class "basename" of the given object / class.
     *
     * @param  string|object  $class
     * @return string
     */
    public static function getClassBaseName($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }

    /**
     * mb_substr wrapper that treats a null $length as "to the end of the
     * string". Native mb_substr only treats null that way since PHP 8.0;
     * on older versions null is cast to (int) 0 and truncates the result.
     *
     * @param  string  $string
     * @param  int  $start
     * @param  int|null  $length
     * @return string
     */
    protected static function mbSubstr($string, $start, $length = null)
    {
        if (is_null($length)) {
            $length = mb_strlen($string, 'UTF-8');
        }

        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * Make a string's first character uppercase (multibyte-safe).
     *
     * @param  string  $string
     * @return string
     */
    public static function ucfirst($string)
    {
        return static::upper(mb_substr($string, 0, 1, 'UTF-8')) . static::mbSubstr($string, 1);
    }

    /**
     * Convert a value to studly caps case (PascalCase).
     *
     * @param  string  $string
     * @return string
     */
    public static function studly($string)
    {
        $words = explode(' ', str_replace(array('-', '_'), ' ', $string));

        $studlyWords = array_map(array('Wilkques\Helpers\Strings', 'ucfirst'), $words);

        return implode('', $studlyWords);
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param  string  $value
     * @param  int  $limit
     * @param  string  $end
     * @return string
     */
    public static function limit($value, $limit = 100, $end = '...')
    {
        if (mb_strlen($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $limit, 'UTF-8')) . $end;
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * Note: unlike Laravel's Str::slug, this does not transliterate
     * accented/non-latin characters to ASCII, it relies on PCRE unicode
     * properties (\pL/\pN) to keep letters/numbers of any script.
     *
     * @param  string  $title
     * @param  string  $separator
     * @return string
     */
    public static function slug($title, $separator = '-')
    {
        $flip = $separator === '-' ? '_' : '-';

        $title = preg_replace('![' . preg_quote($flip, '!') . ']+!u', $separator, $title);

        $title = str_replace('@', $separator . 'at' . $separator, $title);

        $title = preg_replace('![^' . preg_quote($separator, '!') . '\pL\pN\s]+!u', '', static::lower($title));

        $title = preg_replace('![' . preg_quote($separator, '!') . '\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

    /**
     * Masks a portion of a string with a repeated character.
     *
     * @param  string  $string
     * @param  string  $character
     * @param  int  $index
     * @param  int|null  $length
     * @return string
     */
    public static function mask($string, $character, $index, $length = null)
    {
        if ($character === '') {
            return $string;
        }

        $segment = static::mbSubstr($string, $index, $length);

        if ($segment === '') {
            return $string;
        }

        $strlen = mb_strlen($string, 'UTF-8');

        $startIndex = $index;

        if ($index < 0) {
            $startIndex = $index < -$strlen ? 0 : $strlen + $index;
        }

        $start = mb_substr($string, 0, $startIndex, 'UTF-8');

        $segmentLen = mb_strlen($segment, 'UTF-8');

        $end = static::mbSubstr($string, $startIndex + $segmentLen);

        return $start . str_repeat(mb_substr($character, 0, 1, 'UTF-8'), $segmentLen) . $end;
    }

    /**
     * Pad both sides of a string with another.
     *
     * @param  string  $value
     * @param  int  $length
     * @param  string  $pad
     * @return string
     */
    public static function padBoth($value, $length, $pad = ' ')
    {
        return str_pad($value, $length, $pad, STR_PAD_BOTH);
    }

    /**
     * Pad the left side of a string with another.
     *
     * @param  string  $value
     * @param  int  $length
     * @param  string  $pad
     * @return string
     */
    public static function padLeft($value, $length, $pad = ' ')
    {
        return str_pad($value, $length, $pad, STR_PAD_LEFT);
    }

    /**
     * Pad the right side of a string with another.
     *
     * @param  string  $value
     * @param  int  $length
     * @param  string  $pad
     * @return string
     */
    public static function padRight($value, $length, $pad = ' ')
    {
        return str_pad($value, $length, $pad, STR_PAD_RIGHT);
    }

    /**
     * Convert the given string to title case for each word, separated by spaces.
     *
     * @param  string  $value
     * @return string
     */
    public static function headline($value)
    {
        $value = static::delimiterReplace($value, '_');

        $words = preg_split('/[_\-\s]+/', $value, -1, PREG_SPLIT_NO_EMPTY);

        $words = array_map(array('Wilkques\Helpers\Strings', 'ucfirst'), $words);

        return implode(' ', $words);
    }

    /**
     * Get the number of words a string contains.
     *
     * @param  string  $string
     * @param  string|null  $characters
     * @return int
     */
    public static function wordCount($string, $characters = null)
    {
        return str_word_count($string, 0, $characters);
    }

    /**
     * Determine if a given value is a valid UUID.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function isUuid($value)
    {
        if (!is_string($value)) {
            return false;
        }

        return preg_match('/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/iD', $value) > 0;
    }

    /**
     * Determine if a given value is a valid ULID.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function isUlid($value)
    {
        if (!is_string($value)) {
            return false;
        }

        if (strlen($value) !== 26) {
            return false;
        }

        if (strspn($value, '0123456789ABCDEFGHJKMNPQRSTVWXYZabcdefghjkmnpqrstvwxyz') !== 26) {
            return false;
        }

        return $value[0] <= '7';
    }

    /**
     * Remove all "extra" blank space from the given string.
     *
     * @param  string  $value
     * @return string
     */
    public static function squish($value)
    {
        return trim(preg_replace('/\s+/u', ' ', $value));
    }

    /**
     * Match the casing (upper / ucfirst / lower) of $comparison onto $value.
     *
     * @param  string  $value
     * @param  string  $comparison
     * @return string
     */
    protected static function matchCase($value, $comparison)
    {
        if (static::upper($comparison) === $comparison) {
            return static::upper($value);
        }

        if (static::ucfirst($comparison) === $comparison) {
            return static::ucfirst($value);
        }

        return $value;
    }

    /**
     * Get the plural form of an English word.
     *
     * Note: covers common regular/irregular cases only, it is not a full
     * inflector like Laravel's (which relies on doctrine/inflector).
     *
     * @param  string  $value
     * @param  int  $count
     * @return string
     */
    public static function plural($value, $count = 2)
    {
        if ((int) abs($count) === 1) {
            return $value;
        }

        $lower = static::lower($value);

        if (isset(static::$irregular[$lower])) {
            return static::matchCase(static::$irregular[$lower], $value);
        }

        if (preg_match('/(s|x|z|ch|sh)$/i', $value)) {
            return $value . 'es';
        }

        if (preg_match('/[^aeiou]y$/i', $value)) {
            return substr($value, 0, -1) . 'ies';
        }

        if (preg_match('/(fe?)$/i', $value)) {
            return preg_replace('/fe?$/i', 'ves', $value);
        }

        return $value . 's';
    }

    /**
     * Get the singular form of an English word.
     *
     * Note: covers common regular/irregular cases only, it is not a full
     * inflector like Laravel's (which relies on doctrine/inflector).
     *
     * @param  string  $value
     * @return string
     */
    public static function singular($value)
    {
        $lower = static::lower($value);

        $flipped = array_flip(static::$irregular);

        if (isset($flipped[$lower])) {
            return static::matchCase($flipped[$lower], $value);
        }

        if (preg_match('/(s|x|z|ch|sh)es$/i', $value)) {
            return preg_replace('/es$/i', '', $value);
        }

        if (preg_match('/ies$/i', $value)) {
            return preg_replace('/ies$/i', 'y', $value);
        }

        if (preg_match('/ives$/i', $value)) {
            return preg_replace('/ives$/i', 'ife', $value);
        }

        if (preg_match('/ves$/i', $value)) {
            return preg_replace('/ves$/i', 'f', $value);
        }

        if (preg_match('/s$/i', $value) && !preg_match('/ss$/i', $value)) {
            return preg_replace('/s$/i', '', $value);
        }

        return $value;
    }
}
