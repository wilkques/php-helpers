# Helper for PHP
[![TESTS](https://github.com/wilkques/php-helpers/actions/workflows/github-ci.yml/badge.svg)](https://github.com/wilkques/php-helpers/actions/workflows/github-ci.yml)
[![Latest Stable Version](https://poser.pugx.org/wilkques/php-helper/v/stable)](https://packagist.org/packages/wilkques/php-helper)
[![License](https://poser.pugx.org/wilkques/php-helper/license)](https://packagist.org/packages/wilkques/php-helper)

English | [繁體中文](README_ZH.md)

A dependency-free collection of Laravel-flavoured array, string and collection helpers for PHP, written to stay compatible all the way back to **PHP 5.3**.

## Requirements

- PHP >= 5.3 (tested against 5.3, 5.6, 7.3, 7.4, 8.0, 8.1, 8.2, 8.3)

## Installation

````
composer require wilkques/php-helper
````

## Usage

Everything lives under the `Wilkques\Helpers` namespace as static helper classes (`Arrays`, `Strings`, `Objects`), one instantiable `Collections` class, and a set of global functions (auto-loaded via `src/helpers.php`) that thinly wrap those classes — mirroring Laravel's `Arr::*` / `Str::*` / `array_*` / `str_*` / `data_*` helper split.

```php
use Wilkques\Helpers\Arrays;
use Wilkques\Helpers\Strings;

// dot notation get/set, works on arrays and array-ish objects (ArrayAccess)
$value = Arrays::get(['user' => ['name' => 'Wilkques']], 'user.name'); // 'Wilkques'
Arrays::set($data, 'user.email', 'a@b.com');

// case conversion
Strings::snake('helloWorld');  // 'hello_world'
Strings::camel('hello_world'); // 'helloWorld'
Strings::slug('Hello World!'); // 'hello-world'

// same functionality via global helpers
array_get(['user' => ['name' => 'Wilkques']], 'user.name');
str_snake('helloWorld');
```

### More `Arrays` examples

```php
$users = [
    ['id' => 1, 'name' => 'Alice'],
    ['id' => 2, 'name' => 'Bob'],
];

Arrays::pluck($users, 'name', 'id'); // [1 => 'Alice', 2 => 'Bob']

$rows = [['age' => 30], ['age' => 20], ['age' => 25]];
Arrays::sort($rows, 'age'); // sorted ascending by the 'age' key of each row

list($even, $odd) = Arrays::partition([1, 2, 3, 4, 5], function ($n) {
    return $n % 2 === 0;
});
// $even = [2, 4], $odd = [1, 3, 5]

Arrays::flatten(['a' => 1, [2, 3]]); // [1, 2, 3]
Arrays::take([1, 2, 3, 4], 2);       // [1, 2]
Arrays::take([1, 2, 3, 4], -2);      // [3, 4]
Arrays::wrap(null);                  // []
Arrays::wrap('a');                   // ['a']
```

### More `Strings` examples

```php
Strings::slug('Hello World!');                        // 'hello-world'
Strings::mask('taylor@example.com', '*', 3);           // 'tay***************'
Strings::limit('The quick brown fox', 9);              // 'The quick...'
Strings::plural('box');                                 // 'boxes'
Strings::singular('boxes');                              // 'box'
Strings::headline('email_verified_at');                 // 'Email Verified At'
```

### `Objects` — dot notation across mixed arrays *and* objects

`Arrays::get`/`set` only walk arrays (and `ArrayAccess`). `Objects::get`/`set` (and the `data_get()`/`data_set()` global helpers built on them) also walk plain object properties, so a single dot-notation path can cross both in the same call:

```php
use Wilkques\Helpers\Objects;

$config = (object) ['db' => ['host' => 'localhost']];

Objects::get($config, 'db.host'); // 'localhost' — object property, then array key

Objects::set($config, 'db.port', 5432);
$config->db['port']; // 5432

// same thing via the global helper
data_get($config, 'db.host');
```

### Collections

`collect()` (or `new Collections($items)`) wraps an array, JSON string, `Traversable`, or `JsonSerializable` into a chainable, immutable-per-call collection — implementing `Countable`, `IteratorAggregate` and `ArrayAccess`, similar to Laravel's `Collection`.

```php
$total = collect([1, 2, 3, 4])
    ->filter(function ($n) { return $n % 2 === 0; })
    ->map(function ($n) { return $n * 10; })
    ->reduce(function ($carry, $n) { return $carry + $n; }, 0);
// 60

foreach (collect(['a' => 1, 'b' => 2]) as $key => $value) {
    // ...
}
```

Transformation methods (`map`, `filter`, `reject`, `pluck`, `sortBy`, `groupBy`, `chunk`, ...) return a **new** `Collections` instance and never mutate the original — the same as Laravel. Only the explicitly mutating methods (`push`, `put`, `set`, `forget`, `pull`, `prepend`) modify the collection in place.

```php
$people = collect([
    ['role' => 'admin', 'name' => 'Alice'],
    ['role' => 'user',  'name' => 'Bob'],
    ['role' => 'admin', 'name' => 'Carol'],
]);

$adminNames = $people->groupBy('role')->get('admin')->pluck('name')->all();
// ['Alice', 'Carol']

$people->sortBy('name')->pluck('name')->all();
// ['Alice', 'Bob', 'Carol']

collect([1, 2, 2, 3, 3, 3])->unique()->values()->all(); // [1, 2, 3]

collect([1, 2, 3, 4, 5])->chunk(2)->count(); // 3

collect([1, 2, 3])->contains(function ($n) { return $n > 2; }); // true

// the mutating methods operate on $this and return it, for chaining
$cart = collect(['total' => 0]);
$cart->set('total', 100)->set('currency', 'USD');
$cart->all(); // ['total' => 100, 'currency' => 'USD']
```

## API Reference

### `Wilkques\Helpers\Arrays`

Dot-notation aware array helpers, largely mirroring Laravel's `Illuminate\Support\Arr`.

| Method | Description |
| --- | --- |
| `only($array, $keys)` | Get a subset of items by key. |
| `except($array, $keys)` | Get all items except the given keys. |
| `get($array, $key, $default = null)` | Get a value using dot notation. |
| `set(&$array, $key, $value)` | Set a value using dot notation. |
| `has($array, $keys)` | Determine if one or more keys exist (dot notation). |
| `forget(&$array, $keys)` | Remove one or more keys (dot notation). |
| `pull(&$array, $key, $default = null)` | Get a value and remove it from the array. |
| `exists($array, $key)` | Check if a key exists (array or `ArrayAccess`). |
| `accessible($value)` | Determine if a value is array-accessible. |
| `isIterable($value)` | Determine if a value is an array or `Traversable`. |
| `isList($array)` | Determine if an array is a sequential, zero-based list. |
| `isAssoc($array)` | Determine if an array is associative (not a list). |
| `map($array, $callback)` | Map over an array, callback receives `($value, $key)`. |
| `mapWithKeys($array, $callback)` | Map and re-key in one pass. |
| `where($array, $callback)` / `filter($array, $callback = null)` | Filter by callback (any `callable`), or `array_filter` when omitted. |
| `whereNotNull($array)` | Filter out `null` values. |
| `reduce($array, $callback, $initial = null)` | Reduce to a single value. |
| `pluck($array, $value, $key = null, $case = null)` | Pluck a column, optionally keyed and case-converted. |
| `column($array, $columnKey, $indexKey = null)` | `array_column` with dot-notation support. |
| `first($array, $callback = null, $default = null)` | First element, optionally matching a callback. |
| `last($array, $callback = null, $default = null)` | Last element, optionally matching a callback. |
| `flatten($array, $depth = INF)` | Flatten a multi-dimensional array. |
| `collapse($array)` | Collapse an array of arrays into one array. |
| `dot($array, $prepend = '')` | Flatten to single-level dot notation. |
| `undot($array)` | Expand a dot-notation array back out. |
| `divide($array)` | Split into `[keys, values]`. |
| `wrap($value)` | Wrap a non-array value in an array (`null` becomes `[]`). |
| `prepend($array, $value, $key = null)` | Push a value onto the front of an array. |
| `random($array, $number = null, $preserveKeys = false)` | Get one or more random values. |
| `shuffle($array, $seed = null)` | Shuffle an array. |
| `sort($array, $callback = null)` / `sortDesc($array, $callback = null)` | Sort by value, callback, or dot-notation key. |
| `sortRecursive($array, $options = SORT_REGULAR, $descending = false)` | Recursively sort by keys and values. |
| `partition($array, $callback)` | Split into `[passed, failed]` arrays. |
| `crossJoin(...$arrays)` | Cross join arrays into every permutation. |
| `join($array, $glue, $finalGlue = '')` | Join with a different glue before the final item. |
| `take($array, $limit)` | Take the first (or, with a negative limit, last) N items. |
| `query($array)` | Build a URL query string. |
| `replace(...$arrays)` / `replaceRecursive(...$arrays)` | `array_replace(_recursive)` wrappers. |
| `mergeDistinctRecursive(...$arrays)` | Recursively merge, later values win on scalar conflicts. |
| `takeOffRecursive(&$array, $key, $default = null)` | Get and remove a value via dot notation (supports `*`). |
| `value($value, ...$args)` | Resolve a value, calling it if it's a `Closure`. |
| `keySnake($array)` / `keyCamel($array)` | Convert all top-level keys to snake_case / camelCase. |
| `keySnakeToCamel($array)` / `keyKebabCaseToCamel($array)` | Aliases of `keyCamel`. |
| `keyFields($array, $sort)` / `fields($array, $sort)` | Reorder an array to match a given key/value order. |

### `Wilkques\Helpers\Strings`

Mirrors much of Laravel's `Illuminate\Support\Str`, multibyte-safe (`mb_*` under the hood).

| Method | Description |
| --- | --- |
| `contains($haystack, $needles)` | Determine if a string contains any of the given substrings. |
| `startsWith($haystack, $needles)` / `endsWith($haystack, $needles)` | Prefix / suffix checks. |
| `lower($value)` / `upper($value)` | Multibyte case conversion. |
| `ucfirst($value)` | Uppercase the first character (multibyte-safe). |
| `snake($value)` / `kebab($value)` / `camel($value)` / `studly($value)` | Case style conversion. |
| `snakeToCamel($value)` / `kebabCaseToCamel($value)` | Explicit case-style conversions. |
| `delimiterReplace($value, $delimiter = '_')` | Insert a delimiter at camelCase boundaries. |
| `convertCase($value, $case = MB_CASE_LOWER)` | `mb_convert_case` wrapper. |
| `slug($title, $separator = '-')` | Generate a URL-friendly slug. |
| `limit($value, $limit = 100, $end = '...')` | Truncate a string to a given length. |
| `mask($string, $character, $index, $length = null)` | Mask a portion of a string. |
| `padLeft` / `padRight` / `padBoth($value, $length, $pad = ' ')` | `str_pad` wrappers. |
| `headline($value)` | Convert to `Title Case With Spaces`. |
| `squish($value)` | Collapse consecutive whitespace and trim. |
| `wordCount($string, $characters = null)` | Count words in a string. |
| `isUuid($value)` / `isUlid($value)` | Validate UUID / ULID format. |
| `plural($value, $count = 2)` / `singular($value)` | Pluralize / singularize common English words (a small hand-picked rule set, **not** a full inflector). |
| `rand($length)` | Generate a random alphanumeric string. |
| `getClassBaseName($class)` | Get the short class name of an object or FQCN. |

### `Wilkques\Helpers\Objects`

Dot-notation get/set that works across **mixed** arrays and objects (property access), backing the `data_get()` / `data_set()` global helpers.

| Method | Description |
| --- | --- |
| `get($target, $key, $default = null)` | Get a value from an array or object using dot notation. |
| `set(&$target, $key, $value, $overwrite = true)` | Set a value on an array or object using dot notation. |
| `exists($array, $key)` | Check if a key exists (array or `ArrayAccess`). |
| `accessible($value)` | Determine if a value is array-accessible. |
| `value($value, ...$args)` | Resolve a value, calling it if it's a `Closure`. |

### `Wilkques\Helpers\Collections`

| Method | Description |
| --- | --- |
| `new Collections($items = [])` / `Collections::make($items = [])` | Build from an array, JSON string, another `Collections`, `Traversable`, or `JsonSerializable`. |
| `all()` / `toArray()` / `toJson($options = 0)` | Export the underlying items. |
| `count()` / `isEmpty()` / `isNotEmpty()` | Size checks. |
| `map($callback)` / `mapWithKeys($callback)` | Transform items (returns a new collection). |
| `filter($callback = null)` / `reject($callback)` / `whereNotNull()` | Filter items (returns a new collection). |
| `pluck($value, $key = null)` | Pluck a column. |
| `values()` / `keys()` / `flip()` | Re-index, extract keys, or swap keys/values. |
| `only($keys)` / `except($keys)` | Subset by key. |
| `merge($items)` | Merge with another array/collection. |
| `unique($key = null)` | Remove duplicates, optionally by key/callback. |
| `flatten($depth = INF)` / `collapse()` | Flatten nested arrays. |
| `sort($callback = null)` / `sortDesc($callback = null)` / `sortBy($callback)` / `sortByDesc($callback)` | Sort by value, callback, or dot-notation key. |
| `chunk($size)` | Split into a collection of collections. |
| `groupBy($groupBy)` / `keyBy($keyBy)` | Group or re-key by a callback or dot-notation key. |
| `implode($value, $glue = null)` | Join scalar items, or pluck-then-join for arrays of arrays. |
| `reduce($callback, $initial = null)` | Reduce to a single value. |
| `each($callback)` | Iterate; return `false` from the callback to stop early. |
| `first($callback = null, $default = null)` / `last($callback = null, $default = null)` | Get the first/last item, optionally matching a callback. |
| `contains($key, $value = null)` | Check for a value, a callback match, or a key/value pair. |
| `get($key, $default = null)` / `has($key)` / `set($key, $value)` / `put($key, $value)` / `forget($keys)` / `pull($key, $default = null)` | Dot-notation array access (mutating). |
| `push($value)` / `prepend($value, $key = null)` | Add items (mutating). |

Implements `Countable`, `IteratorAggregate` and `ArrayAccess`, so `count($collection)`, `foreach`, and `$collection['key']` all work directly.

### Global helper functions

Every method above is also available as a global function, guarded with `function_exists()` so it never conflicts with functions your app or another package already defines (and, on PHP 8+, several — `str_starts_with`, `str_ends_with`, `str_contains`, `is_iterable`, `array_is_list` — simply defer to PHP's own native implementation).

`collect()`, `data_get()`, `data_set()`, `data_where()`, `value()`, `exists()`, `accessible()`, `class_basename()`, `json_error_check()`, `ve()` / `ved()` (debug dump, `var_export`-based), plus `array_*` / `str_*` prefixed wrappers for every `Arrays::*` / `Strings::*` method listed above (e.g. `array_get()`, `array_pluck()`, `array_sort()`, `str_slug()`, `str_studly()`, `str_random()`, ...).

## Testing

````
composer install
vendor/bin/phpunit
````

## License

MIT
