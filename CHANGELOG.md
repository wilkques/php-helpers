# Changelog

All notable changes to this package will be documented in this file.

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

- **Breaking:** `Collections` transformation methods (`map`, `filter`, `reject`, `pluck`, `sortBy`, `groupBy`, `chunk`, ...) now return a **new** `Collections` instance instead of mutating and returning `$this`, matching Laravel's `Collection` semantics. Only the explicitly mutating methods (`push`, `put`, `set`, `forget`, `pull`, `prepend`) still modify the collection in place.

---

Earlier history is available via `git log` and the [git tags](https://github.com/wilkques/php-helpers/tags).
