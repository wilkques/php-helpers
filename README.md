# Helper for PHP
[![TESTS](https://github.com/wilkques/php-helpers/actions/workflows/github-ci.yml/badge.svg)](https://github.com/wilkques/php-helpers/actions/workflows/github-ci.yml)
[![Latest Stable Version](https://poser.pugx.org/wilkques/php-helper/v/stable)](https://packagist.org/packages/wilkques/php-helper)
[![License](https://poser.pugx.org/wilkques/php-helper/license)](https://packagist.org/packages/wilkques/php-helper)

English | [繁體中文](README_ZH.md)

A dependency-free collection of array, string, and collection helpers for PHP, written to stay compatible all the way back to **PHP 5.3**.

## Requirements

- PHP >= 5.3 (tested against 5.3, 5.6, 7.3, 7.4, 8.0, 8.1, 8.2, 8.3)

## Installation

````
composer require wilkques/php-helper
````

## Usage

Everything lives under the `Wilkques\Helpers` namespace as static helper classes (`Arrays`, `Strings`, `Objects`), one instantiable `Collections` class, and a set of global functions (auto-loaded via `src/helpers.php`) that thinly wrap those classes — a class-method (`Arrays::*` / `Strings::*`) plus global-function (`array_*` / `str_*` / `data_*`) split.

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

`{first}`/`{last}` segments resolve to the current target's first/last key (by insertion order); escape a literal `*`, `{first}`, or `{last}` key with a leading backslash:

```php
Objects::get(['one', 'two', 'three'], '{first}'); // 'one'
Objects::get(['one', 'two', 'three'], '{last}');  // 'three'

Objects::get(['{first}' => 'literal'], '\{first}'); // 'literal'
```

### Collections

`collect()` (or `new Collections($items)`) wraps an array, JSON string, `Traversable`, or `JsonSerializable` into a chainable, immutable-per-call collection — implementing `Countable`, `IteratorAggregate` and `ArrayAccess`.

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

Transformation methods (`map`, `filter`, `reject`, `pluck`, `sortBy`, `groupBy`, `chunk`, ...) return a **new** `Collections` instance and never mutate the original. Only the explicitly mutating methods (`push`, `put`, `set`, `forget`, `pull`, `prepend`) modify the collection in place.

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

Every method below has a runnable example (verified on PHP 7.4 and on real PHP 5.3.10).

### `Wilkques\Helpers\Arrays`

Dot-notation aware array helpers.

| Method | Description | Example |
| --- | --- | --- |
| `only($array, $keys)` | Get a subset of items by key. | `Arrays::only(['name' => 'Wilkques', 'age' => 30], ['name']); // ['name' => 'Wilkques']` |
| `except($array, $keys)` | Get all items except the given keys. | `Arrays::except(['name' => 'Wilkques', 'age' => 30], ['age']); // ['name' => 'Wilkques']` |
| `get($array, $key, $default = null)` | Get a value using dot notation. | `Arrays::get(['user' => ['name' => 'Wilkques']], 'user.name'); // 'Wilkques'` |
| `set(&$array, $key, $value)` | Set a value using dot notation. | `Arrays::set($array, 'user.name', 'Wilkques'); // $array === ['user' => ['name' => 'Wilkques']]` |
| `has($array, $keys)` | Determine if one or more keys exist (dot notation). | `Arrays::has(['user' => ['name' => 'Wilkques']], 'user.name'); // true` |
| `forget(&$array, $keys)` | Remove one or more keys (dot notation). | `Arrays::forget($array, 'age'); // $array no longer has an 'age' key` |
| `pull(&$array, $key, $default = null)` | Get a value and remove it from the array. | `Arrays::pull($array, 'age'); // 30 (and removed from $array)` |
| `exists($array, $key)` | Check if a key exists (array or `ArrayAccess`). | `Arrays::exists(['name' => 'Wilkques'], 'name'); // true` |
| `accessible($value)` | Determine if a value is array-accessible. | `Arrays::accessible([]); // true` |
| `isIterable($value)` | Determine if a value is an array or `Traversable`. | `Arrays::isIterable(new ArrayIterator([])); // true` |
| `isList($array)` | Determine if an array is a sequential, zero-based list. | `Arrays::isList([1, 2, 3]); // true` |
| `isAssoc($array)` | Determine if an array is associative (not a list). | `Arrays::isAssoc(['a' => 1]); // true` |
| `map($array, $callback)` | Map over an array, callback receives `($value, $key)`. | `Arrays::map([1, 2], function ($v, $k) { return $v * 10; }); // [10, 20]` |
| `mapWithKeys($array, $callback)` | Map and re-key in one pass. | `Arrays::mapWithKeys([['id' => 1, 'name' => 'a']], function ($v) { return [$v['id'] => $v['name']]; }); // [1 => 'a']` |
| `where($array, $callback)` | Filter by callback. | `Arrays::where([0, 1, 2, 0, 3], function ($v) { return $v > 0; }); // [1 => 1, 2 => 2, 4 => 3]` |
| `filter($array, $callback = null)` | Filter by callback (any `callable`), or `array_filter` when omitted. | `Arrays::filter([0, 1, 2, '', 3]); // [1 => 1, 2 => 2, 4 => 3]` |
| `whereNotNull($array)` | Filter out `null` values. | `Arrays::whereNotNull([1, null, 2]); // [0 => 1, 2 => 2]` |
| `reduce($array, $callback, $initial = null)` | Reduce to a single value. | `Arrays::reduce([1, 2, 3], function ($c, $i) { return $c + $i; }, 0); // 6` |
| `pluck($array, $value, $key = null, $case = null)` | Pluck a column, optionally keyed and case-converted. | `Arrays::pluck([['id' => 1, 'name' => 'Alice']], 'name', 'id'); // [1 => 'Alice']` |
| `column($array, $columnKey, $indexKey = null)` | `array_column` with dot-notation support. | `Arrays::column([['id' => 1, 'name' => 'Alice']], 'name', 'id'); // [1 => 'Alice']` |
| `first($array, $callback = null, $default = null)` | First element, optionally matching a callback. | `Arrays::first([1, 2, 3], function ($v) { return $v > 1; }); // 2` |
| `last($array, $callback = null, $default = null)` | Last element, optionally matching a callback. | `Arrays::last([1, 2, 3], function ($v) { return $v < 3; }); // 2` |
| `flatten($array, $depth = INF)` | Flatten a multi-dimensional array. | `Arrays::flatten(['a' => 1, [2, 3]]); // [1, 2, 3]` |
| `collapse($array)` | Collapse an array of arrays into one array. | `Arrays::collapse([[1, 2], [3, 4]]); // [1, 2, 3, 4]` |
| `dot($array, $prepend = '')` | Flatten to single-level dot notation. | `Arrays::dot(['user' => ['name' => 'Wilkques']]); // ['user.name' => 'Wilkques']` |
| `undot($array)` | Expand a dot-notation array back out. | `Arrays::undot(['user.name' => 'Wilkques']); // ['user' => ['name' => 'Wilkques']]` |
| `divide($array)` | Split into `[keys, values]`. | `Arrays::divide(['name' => 'Wilkques', 'age' => 30]); // [['name', 'age'], ['Wilkques', 30]]` |
| `wrap($value)` | Wrap a non-array value in an array (`null` becomes `[]`). | `Arrays::wrap('a'); // ['a']` and `Arrays::wrap(null); // []` |
| `prepend($array, $value, $key = null)` | Push a value onto the front of an array. | `Arrays::prepend([1, 2], 0); // [0, 1, 2]` |
| `random($array, $number = null, $preserveKeys = false)` | Get one or more random values. | `Arrays::random([1, 2, 3], 2); // e.g. [2, 1]` |
| `shuffle($array, $seed = null)` | Shuffle an array. | `Arrays::shuffle([1, 2, 3]); // e.g. [3, 1, 2]` |
| `sort($array, $callback = null)` | Sort by value, callback, or dot-notation key. | `Arrays::sort([3, 1, 2]); // [1 => 1, 2 => 2, 0 => 3]` |
| `sortDesc($array, $callback = null)` | Sort descending. | `Arrays::sortDesc([1, 3, 2]); // [1 => 3, 2 => 2, 0 => 1]` |
| `sortRecursive($array, $options = SORT_REGULAR, $descending = false)` | Recursively sort by keys and values. | `Arrays::sortRecursive(['b' => [3, 1, 2], 'a' => 1]); // ['a' => 1, 'b' => [1, 2, 3]]` |
| `partition($array, $callback)` | Split into `[passed, failed]` arrays. | `list($even, $odd) = Arrays::partition([1, 2, 3, 4], function ($n) { return $n % 2 === 0; }); // $even = [1 => 2, 3 => 4]` |
| `crossJoin(...$arrays)` | Cross join arrays into every permutation. | `Arrays::crossJoin([1, 2], ['a', 'b']); // [[1,'a'],[1,'b'],[2,'a'],[2,'b']]` |
| `join($array, $glue, $finalGlue = '')` | Join with a different glue before the final item. | `Arrays::join(['a', 'b', 'c'], ', ', ' and '); // 'a, b and c'` |
| `take($array, $limit)` | Take the first (or, with a negative limit, last) N items. | `Arrays::take([1, 2, 3, 4], -2); // [3, 4]` |
| `query($array)` | Build a URL query string. | `Arrays::query(['foo' => 'bar', 'baz' => 'qux']); // 'foo=bar&baz=qux'` |
| `replace(...$arrays)` | `array_replace` wrapper. | `Arrays::replace(['a' => 1, 'b' => 2], ['b' => 3]); // ['a' => 1, 'b' => 3]` |
| `replaceRecursive(...$arrays)` | `array_replace_recursive` wrapper. | `Arrays::replaceRecursive(['a' => ['x' => 1]], ['a' => ['y' => 2]]); // ['a' => ['x' => 1, 'y' => 2]]` |
| `mergeDistinctRecursive(...$arrays)` | Recursively merge, later values win on scalar conflicts. | `Arrays::mergeDistinctRecursive(['a' => 1], ['b' => 2]); // ['a' => 1, 'b' => 2]` |
| `takeOffRecursive(&$array, $key, $default = null)` | Get and remove a value via dot notation (supports `*`). | `Arrays::takeOffRecursive($array, 'user.name'); // 'Wilkques' (and removed from $array)` |
| `value($value, ...$args)` | Resolve a value, calling it if it's a `Closure`. | `Arrays::value(function () { return 'bar'; }); // 'bar'` |
| `keySnake($array)` | Convert all top-level keys to snake_case. | `Arrays::keySnake(['userName' => 'Wilkques']); // ['user_name' => 'Wilkques']` |
| `keyCamel($array)` | Convert all top-level keys to camelCase. | `Arrays::keyCamel(['user_name' => 'Wilkques']); // ['userName' => 'Wilkques']` |
| `keySnakeToCamel($array)` | Alias of `keyCamel`. | `Arrays::keySnakeToCamel(['user_name' => 'Wilkques']); // ['userName' => 'Wilkques']` |
| `keyKebabCaseToCamel($array)` | Alias of `keyCamel`. | `Arrays::keyKebabCaseToCamel(['user-name' => 'Wilkques']); // ['userName' => 'Wilkques']` |
| `keyFields($array, $sort)` | Reorder an array to match a given key order. | `array_keys(Arrays::keyFields(['b' => 2, 'a' => 1], ['a', 'b'])); // ['a', 'b']` |
| `fields($array, $sort)` | Reorder an array to match a given value order. | `array_values(Arrays::fields(['b' => 2, 'a' => 1], [1, 2])); // [1, 2]` |

### `Wilkques\Helpers\Strings`

Multibyte-safe string helpers (`mb_*` under the hood).

| Method | Description | Example |
| --- | --- | --- |
| `contains($haystack, $needles)` | Determine if a string contains any of the given substrings. | `Strings::contains('hello world', 'world'); // true` |
| `startsWith($haystack, $needles)` | Prefix check. | `Strings::startsWith('hello world', 'hello'); // true` |
| `endsWith($haystack, $needles)` | Suffix check. | `Strings::endsWith('hello world', 'world'); // true` |
| `lower($value)` | Multibyte-safe lowercase. | `Strings::lower('HELLO'); // 'hello'` |
| `upper($value)` | Multibyte-safe uppercase. | `Strings::upper('hello'); // 'HELLO'` |
| `ucfirst($value)` | Uppercase the first character (multibyte-safe). | `Strings::ucfirst('hello'); // 'Hello'` |
| `snake($value)` | Convert to snake_case. | `Strings::snake('helloWorld'); // 'hello_world'` |
| `kebab($value)` | Convert to kebab-case. | `Strings::kebab('helloWorld'); // 'hello-world'` |
| `camel($value)` | Convert to camelCase. | `Strings::camel('hello_world'); // 'helloWorld'` |
| `studly($value)` | Convert to StudlyCase (PascalCase). | `Strings::studly('hello_world'); // 'HelloWorld'` |
| `snakeToCamel($value)` | Explicit snake_case -> camelCase. | `Strings::snakeToCamel('hello_world'); // 'helloWorld'` |
| `kebabCaseToCamel($value)` | Explicit kebab-case -> camelCase. | `Strings::kebabCaseToCamel('hello-world'); // 'helloWorld'` |
| `delimiterReplace($value, $delimiter = '_')` | Insert a delimiter at camelCase boundaries. | `Strings::delimiterReplace('helloWorld', '-'); // 'hello-world'` |
| `convertCase($value, $case = MB_CASE_LOWER)` | `mb_convert_case` wrapper. | `Strings::convertCase('hello', MB_CASE_UPPER); // 'HELLO'` |
| `slug($title, $separator = '-')` | Generate a URL-friendly slug. | `Strings::slug('Hello World!'); // 'hello-world'` |
| `limit($value, $limit = 100, $end = '...')` | Truncate a string to a given length. | `Strings::limit('The quick brown fox', 9); // 'The quick...'` |
| `mask($string, $character, $index, $length = null)` | Mask a portion of a string. | `Strings::mask('taylor@example.com', '*', 3); // 'tay***************'` |
| `padLeft($value, $length, $pad = ' ')` | `str_pad` (`STR_PAD_LEFT`) wrapper. | `Strings::padLeft('7', 3, '0'); // '007'` |
| `padRight($value, $length, $pad = ' ')` | `str_pad` (`STR_PAD_RIGHT`) wrapper. | `Strings::padRight('7', 3, '0'); // '700'` |
| `padBoth($value, $length, $pad = ' ')` | `str_pad` (`STR_PAD_BOTH`) wrapper. | `Strings::padBoth('7', 5, '-'); // '--7--'` |
| `headline($value)` | Convert to `Title Case With Spaces`. | `Strings::headline('email_verified_at'); // 'Email Verified At'` |
| `squish($value)` | Collapse consecutive whitespace and trim. | `Strings::squish('  hello    world  '); // 'hello world'` |
| `wordCount($string, $characters = null)` | Count words in a string. | `Strings::wordCount('hello world foo'); // 3` |
| `isUuid($value)` | Validate UUID format. | `Strings::isUuid('9f8f8f8f-1234-4321-abcd-1234567890ab'); // true` |
| `isUlid($value)` | Validate ULID format. | `Strings::isUlid('01ARZ3NDEKTSV4RRFFQ69G5FAV'); // true` |
| `plural($value, $count = 2)` | Pluralize a common English word (small hand-picked rule set, **not** a full inflector). | `Strings::plural('box'); // 'boxes'` |
| `singular($value)` | Singularize a common English word (same caveat as `plural`). | `Strings::singular('boxes'); // 'box'` |
| `rand($length)` | Generate a random alphanumeric string. | `strlen(Strings::rand(8)); // 8` |
| `getClassBaseName($class)` | Get the short class name of an object or FQCN. | `Strings::getClassBaseName('Wilkques\Helpers\Objects'); // 'Objects'` |

### `Wilkques\Helpers\Objects`

Dot-notation get/set that works across **mixed** arrays and objects (property access), backing the `data_get()` / `data_set()` global helpers.

| Method | Description | Example |
| --- | --- | --- |
| `get($target, $key, $default = null)` | Get a value from an array or object using dot notation. Supports `{first}`/`{last}` directives and `\*`/`\{first}`/`\{last}` escaping. | `Objects::get($config, 'db.host'); // 'localhost'` |
| `set(&$target, $key, $value, $overwrite = true)` | Set a value on an array or object using dot notation. | `Objects::set($config, 'db.port', 5432); // $config->db['port'] === 5432` |
| `exists($array, $key)` | Check if a key exists (array or `ArrayAccess`). | `Objects::exists(['name' => 'Wilkques'], 'name'); // true` |
| `accessible($value)` | Determine if a value is array-accessible. | `Objects::accessible([]); // true` |
| `value($value, ...$args)` | Resolve a value, calling it if it's a `Closure`. | `Objects::value(function () { return 'bar'; }); // 'bar'` |

### `Wilkques\Helpers\Collections`

| Method | Description | Example |
| --- | --- | --- |
| `new Collections($items = [])` | Build from an array, JSON string, another `Collections`, `Traversable`, or `JsonSerializable`. | `(new Collections([1, 2]))->all(); // [1, 2]` |
| `Collections::make($items = [])` | Same as `new Collections()`, static form. | `Collections::make([1, 2])->all(); // [1, 2]` |
| `all()` | Get the underlying items array. | `collect([1, 2, 3])->all(); // [1, 2, 3]` |
| `toArray()` | Recursively convert to a plain array. | `collect(['a' => collect([1, 2])])->toArray(); // ['a' => [1, 2]]` |
| `toJson($options = 0)` | JSON-encode the items. | `collect(['a' => 1])->toJson(); // '{"a":1}'` |
| `count()` | Number of items. | `collect([1, 2, 3])->count(); // 3` |
| `isEmpty()` | Whether the collection has no items. | `collect([])->isEmpty(); // true` |
| `isNotEmpty()` | Whether the collection has items. | `collect([1])->isNotEmpty(); // true` |
| `map($callback)` | Transform each item (returns a new collection). | `collect([1, 2])->map(function ($n) { return $n * 10; })->all(); // [10, 20]` |
| `mapWithKeys($callback)` | Transform and re-key in one pass. | `collect([['id' => 1, 'name' => 'a']])->mapWithKeys(function ($i) { return [$i['id'] => $i['name']]; })->all(); // [1 => 'a']` |
| `filter($callback = null)` | Keep items matching the callback. | `collect([1, 2, 3, 4])->filter(function ($n) { return $n % 2 === 0; })->all(); // [1 => 2, 3 => 4]` |
| `reject($callback)` | Drop items matching the callback (inverse of `filter`). | `collect([1, 2, 3, 4])->reject(function ($n) { return $n % 2 === 0; })->all(); // [0 => 1, 2 => 3]` |
| `whereNotNull()` | Drop `null` items. | `collect([1, null, 2])->whereNotNull()->all(); // [0 => 1, 2 => 2]` |
| `pluck($value, $key = null)` | Pluck a column. | `collect([['id' => 1, 'name' => 'a']])->pluck('name', 'id')->all(); // [1 => 'a']` |
| `values()` | Re-index sequentially. | `collect(['a' => 1, 'b' => 2])->values()->all(); // [1, 2]` |
| `keys()` | Get all the keys. | `collect(['a' => 1, 'b' => 2])->keys()->all(); // ['a', 'b']` |
| `flip()` | Swap keys and values. | `collect(['a' => 1])->flip()->all(); // [1 => 'a']` |
| `only($keys)` | Keep only the given keys. | `collect(['a' => 1, 'b' => 2])->only('a')->all(); // ['a' => 1]` |
| `except($keys)` | Drop the given keys. | `collect(['a' => 1, 'b' => 2])->except('a')->all(); // ['b' => 2]` |
| `merge($items)` | Merge with another array/collection. | `collect(['a' => 1])->merge(['b' => 2])->all(); // ['a' => 1, 'b' => 2]` |
| `unique($key = null)` | Remove duplicates, optionally by key/callback. | `collect([1, 2, 2, 3])->unique()->all(); // [0 => 1, 1 => 2, 3 => 3]` |
| `flatten($depth = INF)` | Flatten nested arrays. | `collect(['a' => 1, [2, 3]])->flatten()->all(); // [1, 2, 3]` |
| `collapse()` | Collapse a collection of arrays into one level. | `collect([[1, 2], [3, 4]])->collapse()->all(); // [1, 2, 3, 4]` |
| `sort($callback = null)` | Sort by value, callback, or dot-notation key. | `collect([3, 1, 2])->sort()->all(); // [1 => 1, 2 => 2, 0 => 3]` |
| `sortDesc($callback = null)` | Sort descending. | `collect([1, 3, 2])->sortDesc()->all(); // [1 => 3, 2 => 2, 0 => 1]` |
| `sortBy($callback, $options = SORT_REGULAR, $descending = false)` | Sort by a key/callback, or by an array of `[key, 'asc'\|'desc']` pairs for multi-column sort with tie-breaking. `$options` accepts the usual `SORT_*` flags. | `collect([['age' => 30, 'name' => 'b'], ['age' => 30, 'name' => 'a'], ['age' => 20, 'name' => 'c']])->sortBy([['age', 'desc'], 'name'])->pluck('name')->all(); // ['a', 'b', 'c']` |
| `sortByDesc($callback, $options = SORT_REGULAR)` | Sort descending; accepts the same key/callback/multi-column-array forms as `sortBy()`. | `collect([['age' => 30], ['age' => 20]])->sortByDesc('age')->pluck('age')->all(); // [30, 20]` |
| `chunk($size)` | Split into a collection of collections. | `collect([1, 2, 3, 4, 5])->chunk(2)->count(); // 3 (sizes 2, 2, 1)` |
| `groupBy($groupBy, $preserveKeys = false)` | Group by a callback or dot-notation key, or by an array of them for multi-level nested grouping. A retriever may also return an array of keys to put one item into multiple groups. `$preserveKeys` keeps original array keys instead of re-indexing. | `collect([['type' => 'a', 'sub' => 'x'], ['type' => 'a', 'sub' => 'y'], ['type' => 'b', 'sub' => 'x']])->groupBy(['type', 'sub'])->get('a')->get('x')->count(); // 1` |
| `keyBy($keyBy)` | Re-key by a callback or dot-notation key. | `collect([['id' => 1, 'name' => 'a']])->keyBy('id')->get(1); // ['id' => 1, 'name' => 'a']` |
| `implode($value, $glue = null)` | Join scalar items, or pluck-then-join for arrays of arrays. | `collect(['a', 'b', 'c'])->implode(','); // 'a,b,c'` |
| `reduce($callback, $initial = null)` | Reduce to a single value. | `collect([1, 2, 3])->reduce(function ($c, $n) { return $c + $n; }, 0); // 6` |
| `each($callback)` | Iterate; return `false` to stop early. | `collect([1, 2, 3])->each(function ($n) { echo $n; }); // prints 123` |
| `first($callback = null, $default = null)` | First item, optionally matching a callback. | `collect([1, 2, 3])->first(); // 1` |
| `last($callback = null, $default = null)` | Last item, optionally matching a callback. | `collect([1, 2, 3])->last(); // 3` |
| `contains($key, $value = null)` | Check for a value, a callback match, or a key/value pair. | `collect([1, 2, 3])->contains(2); // true` |
| `get($key, $default = null)` | Get a value via dot notation. | `collect(['a' => 1])->get('a'); // 1` |
| `has($key)` | Check a key exists via dot notation. | `collect(['a' => 1])->has('a'); // true` |
| `set($key, $value)` | Set a value (mutating, returns `$this`). | `$c->set('b', 2); // $c->get('b') === 2` |
| `put($key, $value)` | Alias of `set()`. | `$c->put('a', 1); // $c->get('a') === 1` |
| `forget($keys)` | Remove one or more keys (mutating). | `$c->forget('a'); // $c no longer has 'a'` |
| `pull($key, $default = null)` | Get a value and remove it (mutating). | `$c->pull('a'); // returns the value, and removes 'a'` |
| `push($value)` | Append a value (mutating). | `collect([1, 2])->push(3)->all(); // [1, 2, 3]` |
| `prepend($value, $key = null)` | Prepend a value (mutating). | `collect([1, 2])->prepend(0)->all(); // [0, 1, 2]` |

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
