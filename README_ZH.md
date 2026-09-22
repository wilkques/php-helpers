# Helper for PHP
[![TESTS](https://github.com/wilkques/php-helpers/actions/workflows/github-ci.yml/badge.svg)](https://github.com/wilkques/php-helpers/actions/workflows/github-ci.yml)
[![Latest Stable Version](https://poser.pugx.org/wilkques/php-helper/v/stable)](https://packagist.org/packages/wilkques/php-helper)
[![License](https://poser.pugx.org/wilkques/php-helper/license)](https://packagist.org/packages/wilkques/php-helper)

[English](README.md) | 繁體中文

一個無外部相依、風格參考 Laravel 的 PHP 陣列／字串／集合輔助函式庫，並保持與 **PHP 5.3** 相容。

## 需求

- PHP >= 5.3（已在 5.3、5.6、7.3、7.4、8.0、8.1、8.2、8.3 測試過）

## 安裝

````
composer require wilkques/php-helper
````

## 使用方式

所有功能都放在 `Wilkques\Helpers` 命名空間下：三個靜態輔助類別（`Arrays`、`Strings`、`Objects`）、一個可實例化的 `Collections` 類別，以及一組全域函式（透過 `src/helpers.php` 自動載入），這些全域函式只是薄薄包一層呼叫對應的類別方法——對應的是 Laravel `Arr::*` / `Str::*` / `array_*` / `str_*` / `data_*` 這種 helper 拆分方式。

```php
use Wilkques\Helpers\Arrays;
use Wilkques\Helpers\Strings;

// dot notation 取值/設值，同時支援陣列與類陣列物件（ArrayAccess）
$value = Arrays::get(['user' => ['name' => 'Wilkques']], 'user.name'); // 'Wilkques'
Arrays::set($data, 'user.email', 'a@b.com');

// 大小寫風格轉換
Strings::snake('helloWorld');  // 'hello_world'
Strings::camel('hello_world'); // 'helloWorld'
Strings::slug('Hello World!'); // 'hello-world'

// 同樣的功能也能用全域函式呼叫
array_get(['user' => ['name' => 'Wilkques']], 'user.name');
str_snake('helloWorld');
```

### Collections

`collect()`（或 `new Collections($items)`）可以把陣列、JSON 字串、`Traversable`、或 `JsonSerializable` 包裝成一個可鏈式呼叫、每次操作都回傳新實例（immutable-per-call）的 collection——實作了 `Countable`、`IteratorAggregate`、`ArrayAccess`，用法很接近 Laravel 的 `Collection`。

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

轉換類方法（`map`、`filter`、`reject`、`pluck`、`sortBy`、`groupBy`、`chunk`……）都會回傳**新的** `Collections` 實例，不會動到原本的集合——跟 Laravel 行為一致。只有明確標示為 mutating 的方法（`push`、`put`、`set`、`forget`、`pull`、`prepend`）才會直接修改當前集合。

## API 參考

### `Wilkques\Helpers\Arrays`

支援 dot notation 的陣列輔助方法，大致對應 Laravel 的 `Illuminate\Support\Arr`。

| 方法 | 說明 |
| --- | --- |
| `only($array, $keys)` | 取出指定 key 的子集合。 |
| `except($array, $keys)` | 取出除了指定 key 以外的所有項目。 |
| `get($array, $key, $default = null)` | 用 dot notation 取值。 |
| `set(&$array, $key, $value)` | 用 dot notation 設值。 |
| `has($array, $keys)` | 判斷一個或多個 key 是否存在（dot notation）。 |
| `forget(&$array, $keys)` | 移除一個或多個 key（dot notation）。 |
| `pull(&$array, $key, $default = null)` | 取值後同時把該值從陣列移除。 |
| `exists($array, $key)` | 檢查 key 是否存在（陣列或 `ArrayAccess`）。 |
| `accessible($value)` | 判斷值是否為陣列可存取（array-accessible）。 |
| `isIterable($value)` | 判斷值是否為陣列或 `Traversable`。 |
| `isList($array)` | 判斷陣列是否為從 0 開始的連續數字索引清單。 |
| `isAssoc($array)` | 判斷陣列是否為關聯陣列（非 list）。 |
| `map($array, $callback)` | 對陣列做 map，callback 收到 `($value, $key)`。 |
| `mapWithKeys($array, $callback)` | 一次完成 map 與重新設定 key。 |
| `where($array, $callback)` / `filter($array, $callback = null)` | 用 callback 過濾（接受任意 `callable`），不傳 callback 時等同 `array_filter`。 |
| `whereNotNull($array)` | 過濾掉 `null` 值。 |
| `reduce($array, $callback, $initial = null)` | 歸約成單一值。 |
| `pluck($array, $value, $key = null, $case = null)` | 取出某個欄位，可選擇要不要用另一個欄位當 key、並轉換大小寫風格。 |
| `column($array, $columnKey, $indexKey = null)` | 支援 dot notation 的 `array_column`。 |
| `first($array, $callback = null, $default = null)` | 取第一個元素，可選擇符合 callback 條件的第一個。 |
| `last($array, $callback = null, $default = null)` | 取最後一個元素，可選擇符合 callback 條件的最後一個。 |
| `flatten($array, $depth = INF)` | 攤平多維陣列。 |
| `collapse($array)` | 把「陣列的陣列」合併成單一陣列。 |
| `dot($array, $prepend = '')` | 攤平成單層的 dot notation 陣列。 |
| `undot($array)` | 把 dot notation 陣列展開回原本的巢狀結構。 |
| `divide($array)` | 拆成 `[keys, values]`。 |
| `wrap($value)` | 把非陣列值包成陣列（`null` 會變成 `[]`）。 |
| `prepend($array, $value, $key = null)` | 把值塞到陣列最前面。 |
| `random($array, $number = null, $preserveKeys = false)` | 取得一個或多個隨機值。 |
| `shuffle($array, $seed = null)` | 打亂陣列。 |
| `sort($array, $callback = null)` / `sortDesc($array, $callback = null)` | 依值、callback、或 dot-notation key 排序。 |
| `sortRecursive($array, $options = SORT_REGULAR, $descending = false)` | 依 key 與值遞迴排序。 |
| `partition($array, $callback)` | 拆成 `[通過, 未通過]` 兩個陣列。 |
| `crossJoin(...$arrays)` | 把多個陣列做笛卡兒積（所有排列組合）。 |
| `join($array, $glue, $finalGlue = '')` | 用不同的連接字串串接，最後一項前面用另一個 glue。 |
| `take($array, $limit)` | 取前 N 個（若 limit 為負數則取後 N 個）。 |
| `query($array)` | 組出 URL query string。 |
| `replace(...$arrays)` / `replaceRecursive(...$arrays)` | `array_replace(_recursive)` 包裝。 |
| `mergeDistinctRecursive(...$arrays)` | 遞迴合併，純量值衝突時後者覆蓋前者。 |
| `takeOffRecursive(&$array, $key, $default = null)` | 用 dot notation 取值並移除（支援 `*` 萬用字元）。 |
| `value($value, ...$args)` | 解析一個值，如果是 `Closure` 就呼叫它。 |
| `keySnake($array)` / `keyCamel($array)` | 把所有頂層 key 轉成 snake_case / camelCase。 |
| `keySnakeToCamel($array)` / `keyKebabCaseToCamel($array)` | `keyCamel` 的別名。 |
| `keyFields($array, $sort)` / `fields($array, $sort)` | 依指定的 key/value 順序重新排列陣列。 |

### `Wilkques\Helpers\Strings`

大致對應 Laravel 的 `Illuminate\Support\Str`，底層使用 `mb_*` 函式，支援多位元組字串。

| 方法 | 說明 |
| --- | --- |
| `contains($haystack, $needles)` | 判斷字串是否包含任一給定子字串。 |
| `startsWith($haystack, $needles)` / `endsWith($haystack, $needles)` | 判斷前綴／後綴。 |
| `lower($value)` / `upper($value)` | 多位元組安全的大小寫轉換。 |
| `ucfirst($value)` | 把第一個字元轉大寫（多位元組安全）。 |
| `snake($value)` / `kebab($value)` / `camel($value)` / `studly($value)` | 命名風格轉換。 |
| `snakeToCamel($value)` / `kebabCaseToCamel($value)` | 明確指定來源風格的轉換。 |
| `delimiterReplace($value, $delimiter = '_')` | 在 camelCase 的邊界插入分隔符號。 |
| `convertCase($value, $case = MB_CASE_LOWER)` | `mb_convert_case` 包裝。 |
| `slug($title, $separator = '-')` | 產生 URL 友善的 slug。 |
| `limit($value, $limit = 100, $end = '...')` | 把字串截斷到指定長度。 |
| `mask($string, $character, $index, $length = null)` | 遮蔽字串的一部分。 |
| `padLeft` / `padRight` / `padBoth($value, $length, $pad = ' ')` | `str_pad` 包裝。 |
| `headline($value)` | 轉成「每個字大寫、空白分隔」的標題格式。 |
| `squish($value)` | 把連續空白收合成一個空白並去頭尾空白。 |
| `wordCount($string, $characters = null)` | 計算字串中的單字數。 |
| `isUuid($value)` / `isUlid($value)` | 驗證是否為 UUID / ULID 格式。 |
| `plural($value, $count = 2)` / `singular($value)` | 把常見英文單字轉成複數／單數（只是手動挑選的小規則集，**不是**完整的 inflector）。 |
| `rand($length)` | 產生隨機英數字字串。 |
| `getClassBaseName($class)` | 取得物件或完整類別名稱（FQCN）的短類別名稱。 |

### `Wilkques\Helpers\Objects`

支援**混合陣列與物件**（含物件屬性存取）的 dot notation 取值/設值，是 `data_get()` / `data_set()` 全域函式背後的實作。

| 方法 | 說明 |
| --- | --- |
| `get($target, $key, $default = null)` | 用 dot notation 從陣列或物件取值。 |
| `set(&$target, $key, $value, $overwrite = true)` | 用 dot notation 對陣列或物件設值。 |
| `exists($array, $key)` | 檢查 key 是否存在（陣列或 `ArrayAccess`）。 |
| `accessible($value)` | 判斷值是否為陣列可存取（array-accessible）。 |
| `value($value, ...$args)` | 解析一個值，如果是 `Closure` 就呼叫它。 |

### `Wilkques\Helpers\Collections`

| 方法 | 說明 |
| --- | --- |
| `new Collections($items = [])` / `Collections::make($items = [])` | 從陣列、JSON 字串、另一個 `Collections`、`Traversable`、或 `JsonSerializable` 建立。 |
| `all()` / `toArray()` / `toJson($options = 0)` | 匯出底層的資料。 |
| `count()` / `isEmpty()` / `isNotEmpty()` | 判斷數量／是否為空。 |
| `map($callback)` / `mapWithKeys($callback)` | 轉換項目（回傳新的 collection）。 |
| `filter($callback = null)` / `reject($callback)` / `whereNotNull()` | 過濾項目（回傳新的 collection）。 |
| `pluck($value, $key = null)` | 取出某個欄位。 |
| `values()` / `keys()` / `flip()` | 重新索引、取出所有 key、或互換 key 與 value。 |
| `only($keys)` / `except($keys)` | 依 key 取子集合。 |
| `merge($items)` | 跟另一個陣列／collection 合併。 |
| `unique($key = null)` | 去除重複值，可選擇依 key／callback 判斷。 |
| `flatten($depth = INF)` / `collapse()` | 攤平巢狀陣列。 |
| `sort($callback = null)` / `sortDesc($callback = null)` / `sortBy($callback)` / `sortByDesc($callback)` | 依值、callback、或 dot-notation key 排序。 |
| `chunk($size)` | 拆成一個「裝著多個 collection」的 collection。 |
| `groupBy($groupBy)` / `keyBy($keyBy)` | 依 callback 或 dot-notation key 分組／重新設定 key。 |
| `implode($value, $glue = null)` | 串接純量項目，或先 pluck 再串接（陣列的陣列時）。 |
| `reduce($callback, $initial = null)` | 歸約成單一值。 |
| `each($callback)` | 逐一迭代；callback 回傳 `false` 可提前中止。 |
| `first($callback = null, $default = null)` / `last($callback = null, $default = null)` | 取第一個／最後一個項目，可選擇符合 callback 條件。 |
| `contains($key, $value = null)` | 檢查是否包含某個值、符合某個 callback、或某組 key/value。 |
| `get($key, $default = null)` / `has($key)` / `set($key, $value)` / `put($key, $value)` / `forget($keys)` / `pull($key, $default = null)` | dot notation 陣列存取（會修改當前集合）。 |
| `push($value)` / `prepend($value, $key = null)` | 新增項目（會修改當前集合）。 |

實作了 `Countable`、`IteratorAggregate`、`ArrayAccess`，所以 `count($collection)`、`foreach`、`$collection['key']` 都可以直接使用。

### 全域輔助函式

上面每個方法都有對應的全域函式版本，並且用 `function_exists()` 包住，所以不會跟你自己的應用程式或其他套件已經定義的函式衝突（而且在 PHP 8+ 上，有幾個——`str_starts_with`、`str_ends_with`、`str_contains`、`is_iterable`、`array_is_list`——會直接讓路給 PHP 原生的實作）。

`collect()`、`data_get()`、`data_set()`、`data_where()`、`value()`、`exists()`、`accessible()`、`class_basename()`、`json_error_check()`、`ve()` / `ved()`（除錯用，用 `var_export` 印出來），以及上面列出的每個 `Arrays::*` / `Strings::*` 方法都有對應的 `array_*` / `str_*` 前綴全域函式（例如 `array_get()`、`array_pluck()`、`array_sort()`、`str_slug()`、`str_studly()`、`str_random()`……）。

## 測試

````
composer install
vendor/bin/phpunit
````

## 授權

MIT
