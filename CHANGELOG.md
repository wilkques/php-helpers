# Changelog

All notable changes to this package will be documented in this file.

## [6.1.0]

### Added

- `Arrays::merge()` — thin variadic wrapper over `array_merge()`, matching the existing `replace()`/`replaceRecursive()` pattern.
- `Collections::groupBy($groupBy, $preserveKeys = false)`: `$groupBy` can now be an array of keys/callables for multi-level nested grouping (each level's retriever may also return an array of group keys, putting one item into multiple groups). New `$preserveKeys` param keeps original array keys instead of re-indexing.
- `Collections::sortBy()`/`sortByDesc()`: `$callback` can now be an array of `[key, 'asc'|'desc']` pairs for multi-column sorting with tie-breaking cascades, and `$options` now accepts the usual `SORT_*` flags (`SORT_NUMERIC`, `SORT_STRING`, `SORT_NATURAL`, `SORT_FLAG_CASE`, `SORT_LOCALE_STRING`) for both the single- and multi-column paths.
- `Objects::get()`: dot-paths now support `data_get()`-style `{first}`/`{last}` directives (resolve to the target's first/last key by insertion order) and `\*`/`\{first}`/`\{last}` escaping to treat those as literal keys.

### Fixed

- `Arrays::map()` now preserves keys via `array_combine()` (previously reindexed the result like plain `array_map()`).
- `Collections::reduce()` now passes `$key` as the 3rd callback argument.
- `Collections::contains()`: a plain string key that happens to be `is_callable()` (e.g. `'strlen'`, which resolves to the global function) is now searched for as a literal value, not invoked as a predicate.
- `Arrays::query()`: encodes with `PHP_QUERY_RFC3986` when available (spaces as `%20`), guarded for PHP 5.3 where that constant and the 4-arg `http_build_query()` don't exist yet (falls back to RFC1738, spaces as `+`).
- `Arrays::random()`: `$number <= 0` now returns an empty array instead of falling through to `array_rand()` with an invalid count and a warning.
- `Arrays::exists()`/`Objects::exists()`: float keys are now cast to string before `array_key_exists()`, matching how PHP itself normalizes a float array key.
- `Arrays::forget()`/`Objects::get()`: mid-path traversal now checks `accessible()` instead of `is_array()`, so it can descend through `ArrayAccess` objects, not just plain arrays.
- `Strings::limit()`: now measures/truncates by display width (`mb_strwidth()`/`mb_strimwidth()`) instead of character count — a CJK/full-width string was previously truncated to roughly double the intended visual length.
- `Strings::squish()`: now also collapses the Hangul filler codepoints U+3164/U+1160, which render as blank but aren't matched by `\s`.
- `Strings::padLeft()`/`padRight()`/`padBoth()`: now pad by character count (`mb_strlen()`/`mb_substr()`) instead of byte count — a multibyte string whose *byte* length already exceeded the target previously got zero padding even though its *character* length was well under it.

### Changed

- Performance: `Collections::unique()`, `Arrays::dot()`, `Arrays::fields()`, `Arrays::sort()`/`sortDesc()`, `Strings::singular()`, `Arrays::flatten()`, `Arrays::last()` (with a callback), `Collections::sortByMany()`, and `Arrays::get()`/`Objects::get()` were all rewritten with better time complexity or constant factors, with the same observable behavior (see the two caveats below for the narrow exceptions). Highlights: `Collections::unique()` O(n²) → O(n) (~23x faster on 32k items with no duplicates), `Arrays::dot()`/`Arrays::fields()` quadratic-ish → linear (~113x / ~128x on large inputs), `Arrays::flatten()` O(n × nesting depth) → O(n) (up to ~31x on deeply nested data), `Collections::sortByMany()` ~3.7x on multi-column sorts, and an `Objects::get()` regression from the new `{first}`/`{last}` directive support mostly recovered with a fast-path check.
- `Collections::sortByDesc()`: now preserves the relative order of equal-valued items (stable) instead of the old `array_reverse()`-based approach, which fully reversed ties too — a deliberate, requested behavior change alongside the new multi-column sorting support.
- `Arrays::fields($array, $sort)`: on PHP < 8.0 (5.3–7.4), when multiple `$array` elements tie on the same position in `$sort`, their relative order is now stable (original insertion order) instead of PHP's pre-8.0 unstable `uasort()` order. PHP 8.0+ already behaved this way (`uasort()` became stable there), so this only changes observable output on PHP 5.3–7.4, and only among tied elements.

## [6.0.1]

### Fixed

- `Collections::count()`, `getIterator()`, `offsetExists()`, `offsetGet()`, `offsetSet()`, `offsetUnset()` no longer trigger a PHP 8.1+ deprecation notice (added `#[\ReturnTypeWillChange]`, which parses as a harmless comment on PHP < 8.0, confirmed on real PHP 5.3.10 — same pattern already used by `wilkques/filesystem`).

### Documentation

- Added a Traditional Chinese README (`README_ZH.md`).
- Every documented `Arrays`/`Strings`/`Objects`/`Collections` method now has a verified, runnable inline example.

## [6.0.0]

### Added

- `Wilkques\Helpers\Collections` — a new chainable, immutable-per-call collection class (`collect($items)` / `new Collections($items)`), implementing `Countable`, `IteratorAggregate` and `ArrayAccess`. Supports construction from an array, JSON string, another `Collections`, `Traversable` or `JsonSerializable`.
- `Arrays`: `isList`, `isAssoc`, `random`, `shuffle`, `sort`, `sortDesc`, `sortRecursive`, `query`, `crossJoin`, `partition`, `join`, `take`, `whereNotNull`.
- `Strings`: `ucfirst`, `studly`, `limit`, `slug`, `mask`, `padLeft`, `padRight`, `padBoth`, `headline`, `wordCount`, `isUuid`, `isUlid`, `squish`, `plural`, `singular` (a small hand-picked English rule set, not a full inflector).
- Global helpers for all of the above (`array_random()`, `array_sort()`, `str_slug()`, `str_studly()`, ... ), plus `collect()`, `str_random()` and `class_basename()`.

### Fixed

- `Arrays::reduce()` now accepts an `$initial` value (previously always called `array_reduce()` without one).
- `Arrays::filter()` now accepts any `callable`, not just `Closure` instances.
- `Collections`' constructor (`getArrayableItems()`) no longer swallows non-array/non-string input (e.g. a plain `Traversable`) into an empty array before ever checking the `Traversable`/`JsonSerializable` branches.
- **Breaking:** the global helper guarded as `str_snake()` actually defined a function named `string_snake()`. It's now correctly named `str_snake()`, matching every other `str_*` helper.
- `tests/Stringstest.php` was renamed to `tests/StringsTest.php` — the previous filename didn't match PHPUnit's `*Test.php` discovery suffix, so the `Strings` test suite was silently never running in CI.

### Changed

- **Breaking:** `Collections` transformation methods (`map`, `filter`, `reject`, `pluck`, `sortBy`, `groupBy`, `chunk`, ...) now return a **new** `Collections` instance instead of mutating and returning `$this`. Only the explicitly mutating methods (`push`, `put`, `set`, `forget`, `pull`, `prepend`) still modify the collection in place.

---

Earlier history is available via `git log` and the [git tags](https://github.com/wilkques/php-helpers/tags).
