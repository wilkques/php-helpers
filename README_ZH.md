# Helper for PHP
[![TESTS](https://github.com/wilkques/php-helpers/actions/workflows/github-ci.yml/badge.svg)](https://github.com/wilkques/php-helpers/actions/workflows/github-ci.yml)
[![Latest Stable Version](https://poser.pugx.org/wilkques/php-helper/v/stable)](https://packagist.org/packages/wilkques/php-helper)
[![License](https://poser.pugx.org/wilkques/php-helper/license)](https://packagist.org/packages/wilkques/php-helper)

[English](README.md) | 繁體中文

一個無外部相依的 PHP 陣列／字串／集合輔助函式庫，並保持與 **PHP 5.3** 相容。

## 需求

- PHP >= 5.3（已在 5.3、5.6、7.3、7.4、8.0、8.1、8.2、8.3 測試過）

## 安裝

````
composer require wilkques/php-helper
````

## 使用方式

所有功能都放在 `Wilkques\Helpers` 命名空間下：三個靜態輔助類別（`Arrays`、`Strings`、`Objects`）、一個可實例化的 `Collections` 類別，以及一組全域函式（透過 `src/helpers.php` 自動載入），這些全域函式只是薄薄包一層呼叫對應的類別方法——分成類別方法（`Arrays::*` / `Strings::*`）跟全域函式（`array_*` / `str_*` / `data_*`）兩種呼叫方式。

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

### 更多 `Arrays` 範例

```php
$users = [
    ['id' => 1, 'name' => 'Alice'],
    ['id' => 2, 'name' => 'Bob'],
];

Arrays::pluck($users, 'name', 'id'); // [1 => 'Alice', 2 => 'Bob']

$rows = [['age' => 30], ['age' => 20], ['age' => 25]];
Arrays::sort($rows, 'age'); // 依每個 row 的 'age' key 由小到大排序

list($even, $odd) = Arrays::partition([1, 2, 3, 4, 5], function ($n) {
    return $n % 2 === 0;
});
// $even = [2, 4]，$odd = [1, 3, 5]

Arrays::flatten(['a' => 1, [2, 3]]); // [1, 2, 3]
Arrays::take([1, 2, 3, 4], 2);       // [1, 2]
Arrays::take([1, 2, 3, 4], -2);      // [3, 4]
Arrays::wrap(null);                  // []
Arrays::wrap('a');                   // ['a']
```

### 更多 `Strings` 範例

```php
Strings::slug('Hello World!');                        // 'hello-world'
Strings::mask('taylor@example.com', '*', 3);           // 'tay***************'
Strings::limit('The quick brown fox', 9);              // 'The quick...'
Strings::plural('box');                                 // 'boxes'
Strings::singular('boxes');                              // 'box'
Strings::headline('email_verified_at');                 // 'Email Verified At'
```

### `Objects` —— 同時支援陣列與物件混合的 dot notation

`Arrays::get`/`set` 只會走陣列（以及 `ArrayAccess`）。`Objects::get`/`set`（以及建構在它們之上的 `data_get()`/`data_set()` 全域函式）連物件屬性也會一併處理，所以同一條 dot-notation 路徑可以在同一次呼叫裡同時跨陣列與物件：

```php
use Wilkques\Helpers\Objects;

$config = (object) ['db' => ['host' => 'localhost']];

Objects::get($config, 'db.host'); // 'localhost' —— 先取物件屬性，再取陣列 key

Objects::set($config, 'db.port', 5432);
$config->db['port']; // 5432

// 用全域函式也一樣
data_get($config, 'db.host');
```

`{first}`/`{last}` 這種 segment 會取目前目標依插入順序的第一個/最後一個 key；若字面上的 key 就叫 `*`、`{first}` 或 `{last}`，前面加反斜線跳脫即可：

```php
Objects::get(['one', 'two', 'three'], '{first}'); // 'one'
Objects::get(['one', 'two', 'three'], '{last}');  // 'three'

Objects::get(['{first}' => 'literal'], '\{first}'); // 'literal'
```

### Collections

`collect()`（或 `new Collections($items)`）可以把陣列、JSON 字串、`Traversable`、或 `JsonSerializable` 包裝成一個可鏈式呼叫、每次操作都回傳新實例（immutable-per-call）的 collection——實作了 `Countable`、`IteratorAggregate`、`ArrayAccess`。

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

轉換類方法（`map`、`filter`、`reject`、`pluck`、`sortBy`、`groupBy`、`chunk`……）都會回傳**新的** `Collections` 實例，不會動到原本的集合。只有明確標示為 mutating 的方法（`push`、`put`、`set`、`forget`、`pull`、`prepend`）才會直接修改當前集合。

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

// mutating 方法會操作 $this 並回傳它自己，可以串接呼叫
$cart = collect(['total' => 0]);
$cart->set('total', 100)->set('currency', 'USD');
$cart->all(); // ['total' => 100, 'currency' => 'USD']
```

## API 參考

以下每個方法都附上實際可執行的範例（已在 PHP 7.4 與真的 PHP 5.3.10 上驗證過）。

### `Wilkques\Helpers\Arrays`

支援 dot notation 的陣列輔助方法。

| 方法 | 說明 | 範例 |
| --- | --- | --- |
| `only($array, $keys)` | 取出指定 key 的子集合。 | `Arrays::only(['name' => 'Wilkques', 'age' => 30], ['name']); // ['name' => 'Wilkques']` |
| `except($array, $keys)` | 取出除了指定 key 以外的所有項目。 | `Arrays::except(['name' => 'Wilkques', 'age' => 30], ['age']); // ['name' => 'Wilkques']` |
| `get($array, $key, $default = null)` | 用 dot notation 取值。 | `Arrays::get(['user' => ['name' => 'Wilkques']], 'user.name'); // 'Wilkques'` |
| `set(&$array, $key, $value)` | 用 dot notation 設值。 | `Arrays::set($array, 'user.name', 'Wilkques'); // $array === ['user' => ['name' => 'Wilkques']]` |
| `has($array, $keys)` | 判斷一個或多個 key 是否存在（dot notation）。 | `Arrays::has(['user' => ['name' => 'Wilkques']], 'user.name'); // true` |
| `forget(&$array, $keys)` | 移除一個或多個 key（dot notation）。 | `Arrays::forget($array, 'age'); // $array 不再有 'age' 這個 key` |
| `pull(&$array, $key, $default = null)` | 取值後同時把該值從陣列移除。 | `Arrays::pull($array, 'age'); // 30（同時從 $array 移除）` |
| `exists($array, $key)` | 檢查 key 是否存在（陣列或 `ArrayAccess`）。 | `Arrays::exists(['name' => 'Wilkques'], 'name'); // true` |
| `accessible($value)` | 判斷值是否為陣列可存取（array-accessible）。 | `Arrays::accessible([]); // true` |
| `isIterable($value)` | 判斷值是否為陣列或 `Traversable`。 | `Arrays::isIterable(new ArrayIterator([])); // true` |
| `isList($array)` | 判斷陣列是否為從 0 開始的連續數字索引清單。 | `Arrays::isList([1, 2, 3]); // true` |
| `isAssoc($array)` | 判斷陣列是否為關聯陣列（非 list）。 | `Arrays::isAssoc(['a' => 1]); // true` |
| `map($array, $callback)` | 對陣列做 map，callback 收到 `($value, $key)`。 | `Arrays::map([1, 2], function ($v, $k) { return $v * 10; }); // [10, 20]` |
| `mapWithKeys($array, $callback)` | 一次完成 map 與重新設定 key。 | `Arrays::mapWithKeys([['id' => 1, 'name' => 'a']], function ($v) { return [$v['id'] => $v['name']]; }); // [1 => 'a']` |
| `where($array, $callback)` | 用 callback 過濾。 | `Arrays::where([0, 1, 2, 0, 3], function ($v) { return $v > 0; }); // [1 => 1, 2 => 2, 4 => 3]` |
| `filter($array, $callback = null)` | 用 callback 過濾（接受任意 `callable`），不傳 callback 時等同 `array_filter`。 | `Arrays::filter([0, 1, 2, '', 3]); // [1 => 1, 2 => 2, 4 => 3]` |
| `whereNotNull($array)` | 過濾掉 `null` 值。 | `Arrays::whereNotNull([1, null, 2]); // [0 => 1, 2 => 2]` |
| `reduce($array, $callback, $initial = null)` | 歸約成單一值。 | `Arrays::reduce([1, 2, 3], function ($c, $i) { return $c + $i; }, 0); // 6` |
| `pluck($array, $value, $key = null, $case = null)` | 取出某個欄位，可選擇要不要用另一個欄位當 key、並轉換大小寫風格。 | `Arrays::pluck([['id' => 1, 'name' => 'Alice']], 'name', 'id'); // [1 => 'Alice']` |
| `column($array, $columnKey, $indexKey = null)` | 支援 dot notation 的 `array_column`。 | `Arrays::column([['id' => 1, 'name' => 'Alice']], 'name', 'id'); // [1 => 'Alice']` |
| `first($array, $callback = null, $default = null)` | 取第一個元素，可選擇符合 callback 條件的第一個。 | `Arrays::first([1, 2, 3], function ($v) { return $v > 1; }); // 2` |
| `last($array, $callback = null, $default = null)` | 取最後一個元素，可選擇符合 callback 條件的最後一個。 | `Arrays::last([1, 2, 3], function ($v) { return $v < 3; }); // 2` |
| `flatten($array, $depth = INF)` | 攤平多維陣列。 | `Arrays::flatten(['a' => 1, [2, 3]]); // [1, 2, 3]` |
| `collapse($array)` | 把「陣列的陣列」合併成單一陣列。 | `Arrays::collapse([[1, 2], [3, 4]]); // [1, 2, 3, 4]` |
| `dot($array, $prepend = '')` | 攤平成單層的 dot notation 陣列。 | `Arrays::dot(['user' => ['name' => 'Wilkques']]); // ['user.name' => 'Wilkques']` |
| `undot($array)` | 把 dot notation 陣列展開回原本的巢狀結構。 | `Arrays::undot(['user.name' => 'Wilkques']); // ['user' => ['name' => 'Wilkques']]` |
| `divide($array)` | 拆成 `[keys, values]`。 | `Arrays::divide(['name' => 'Wilkques', 'age' => 30]); // [['name', 'age'], ['Wilkques', 30]]` |
| `wrap($value)` | 把非陣列值包成陣列（`null` 會變成 `[]`）。 | `Arrays::wrap('a'); // ['a']`，以及 `Arrays::wrap(null); // []` |
| `prepend($array, $value, $key = null)` | 把值塞到陣列最前面。 | `Arrays::prepend([1, 2], 0); // [0, 1, 2]` |
| `random($array, $number = null, $preserveKeys = false)` | 取得一個或多個隨機值。 | `Arrays::random([1, 2, 3], 2); // 例如 [2, 1]` |
| `shuffle($array, $seed = null)` | 打亂陣列。 | `Arrays::shuffle([1, 2, 3]); // 例如 [3, 1, 2]` |
| `sort($array, $callback = null)` | 依值、callback、或 dot-notation key 排序。 | `Arrays::sort([3, 1, 2]); // [1 => 1, 2 => 2, 0 => 3]` |
| `sortDesc($array, $callback = null)` | 由大到小排序。 | `Arrays::sortDesc([1, 3, 2]); // [1 => 3, 2 => 2, 0 => 1]` |
| `sortRecursive($array, $options = SORT_REGULAR, $descending = false)` | 依 key 與值遞迴排序。 | `Arrays::sortRecursive(['b' => [3, 1, 2], 'a' => 1]); // ['a' => 1, 'b' => [1, 2, 3]]` |
| `partition($array, $callback)` | 拆成 `[通過, 未通過]` 兩個陣列。 | `list($even, $odd) = Arrays::partition([1, 2, 3, 4], function ($n) { return $n % 2 === 0; }); // $even = [1 => 2, 3 => 4]` |
| `crossJoin(...$arrays)` | 把多個陣列做笛卡兒積（所有排列組合）。 | `Arrays::crossJoin([1, 2], ['a', 'b']); // [[1,'a'],[1,'b'],[2,'a'],[2,'b']]` |
| `join($array, $glue, $finalGlue = '')` | 用不同的連接字串串接，最後一項前面用另一個 glue。 | `Arrays::join(['a', 'b', 'c'], ', ', ' and '); // 'a, b and c'` |
| `take($array, $limit)` | 取前 N 個（若 limit 為負數則取後 N 個）。 | `Arrays::take([1, 2, 3, 4], -2); // [3, 4]` |
| `query($array)` | 組出 URL query string。 | `Arrays::query(['foo' => 'bar', 'baz' => 'qux']); // 'foo=bar&baz=qux'` |
| `replace(...$arrays)` | `array_replace` 包裝。 | `Arrays::replace(['a' => 1, 'b' => 2], ['b' => 3]); // ['a' => 1, 'b' => 3]` |
| `replaceRecursive(...$arrays)` | `array_replace_recursive` 包裝。 | `Arrays::replaceRecursive(['a' => ['x' => 1]], ['a' => ['y' => 2]]); // ['a' => ['x' => 1, 'y' => 2]]` |
| `mergeDistinctRecursive(...$arrays)` | 遞迴合併，純量值衝突時後者覆蓋前者。 | `Arrays::mergeDistinctRecursive(['a' => 1], ['b' => 2]); // ['a' => 1, 'b' => 2]` |
| `takeOffRecursive(&$array, $key, $default = null)` | 用 dot notation 取值並移除（支援 `*` 萬用字元）。 | `Arrays::takeOffRecursive($array, 'user.name'); // 'Wilkques'（同時從 $array 移除）` |
| `value($value, ...$args)` | 解析一個值，如果是 `Closure` 就呼叫它。 | `Arrays::value(function () { return 'bar'; }); // 'bar'` |
| `keySnake($array)` | 把所有頂層 key 轉成 snake_case。 | `Arrays::keySnake(['userName' => 'Wilkques']); // ['user_name' => 'Wilkques']` |
| `keyCamel($array)` | 把所有頂層 key 轉成 camelCase。 | `Arrays::keyCamel(['user_name' => 'Wilkques']); // ['userName' => 'Wilkques']` |
| `keySnakeToCamel($array)` | `keyCamel` 的別名。 | `Arrays::keySnakeToCamel(['user_name' => 'Wilkques']); // ['userName' => 'Wilkques']` |
| `keyKebabCaseToCamel($array)` | `keyCamel` 的別名。 | `Arrays::keyKebabCaseToCamel(['user-name' => 'Wilkques']); // ['userName' => 'Wilkques']` |
| `keyFields($array, $sort)` | 依指定的 key 順序重新排列陣列。 | `array_keys(Arrays::keyFields(['b' => 2, 'a' => 1], ['a', 'b'])); // ['a', 'b']` |
| `fields($array, $sort)` | 依指定的 value 順序重新排列陣列。 | `array_values(Arrays::fields(['b' => 2, 'a' => 1], [1, 2])); // [1, 2]` |

### `Wilkques\Helpers\Strings`

底層使用 `mb_*` 函式，支援多位元組字串。

| 方法 | 說明 | 範例 |
| --- | --- | --- |
| `contains($haystack, $needles)` | 判斷字串是否包含任一給定子字串。 | `Strings::contains('hello world', 'world'); // true` |
| `startsWith($haystack, $needles)` | 判斷前綴。 | `Strings::startsWith('hello world', 'hello'); // true` |
| `endsWith($haystack, $needles)` | 判斷後綴。 | `Strings::endsWith('hello world', 'world'); // true` |
| `lower($value)` | 多位元組安全的轉小寫。 | `Strings::lower('HELLO'); // 'hello'` |
| `upper($value)` | 多位元組安全的轉大寫。 | `Strings::upper('hello'); // 'HELLO'` |
| `ucfirst($value)` | 把第一個字元轉大寫（多位元組安全）。 | `Strings::ucfirst('hello'); // 'Hello'` |
| `snake($value)` | 轉成 snake_case。 | `Strings::snake('helloWorld'); // 'hello_world'` |
| `kebab($value)` | 轉成 kebab-case。 | `Strings::kebab('helloWorld'); // 'hello-world'` |
| `camel($value)` | 轉成 camelCase。 | `Strings::camel('hello_world'); // 'helloWorld'` |
| `studly($value)` | 轉成 StudlyCase（PascalCase）。 | `Strings::studly('hello_world'); // 'HelloWorld'` |
| `snakeToCamel($value)` | 明確指定從 snake_case 轉 camelCase。 | `Strings::snakeToCamel('hello_world'); // 'helloWorld'` |
| `kebabCaseToCamel($value)` | 明確指定從 kebab-case 轉 camelCase。 | `Strings::kebabCaseToCamel('hello-world'); // 'helloWorld'` |
| `delimiterReplace($value, $delimiter = '_')` | 在 camelCase 的邊界插入分隔符號。 | `Strings::delimiterReplace('helloWorld', '-'); // 'hello-world'` |
| `convertCase($value, $case = MB_CASE_LOWER)` | `mb_convert_case` 包裝。 | `Strings::convertCase('hello', MB_CASE_UPPER); // 'HELLO'` |
| `slug($title, $separator = '-')` | 產生 URL 友善的 slug。 | `Strings::slug('Hello World!'); // 'hello-world'` |
| `limit($value, $limit = 100, $end = '...')` | 把字串截斷到指定長度。 | `Strings::limit('The quick brown fox', 9); // 'The quick...'` |
| `mask($string, $character, $index, $length = null)` | 遮蔽字串的一部分。 | `Strings::mask('taylor@example.com', '*', 3); // 'tay***************'` |
| `padLeft($value, $length, $pad = ' ')` | `str_pad`（`STR_PAD_LEFT`）包裝。 | `Strings::padLeft('7', 3, '0'); // '007'` |
| `padRight($value, $length, $pad = ' ')` | `str_pad`（`STR_PAD_RIGHT`）包裝。 | `Strings::padRight('7', 3, '0'); // '700'` |
| `padBoth($value, $length, $pad = ' ')` | `str_pad`（`STR_PAD_BOTH`）包裝。 | `Strings::padBoth('7', 5, '-'); // '--7--'` |
| `headline($value)` | 轉成「每個字大寫、空白分隔」的標題格式。 | `Strings::headline('email_verified_at'); // 'Email Verified At'` |
| `squish($value)` | 把連續空白收合成一個空白並去頭尾空白。 | `Strings::squish('  hello    world  '); // 'hello world'` |
| `wordCount($string, $characters = null)` | 計算字串中的單字數。 | `Strings::wordCount('hello world foo'); // 3` |
| `isUuid($value)` | 驗證是否為 UUID 格式。 | `Strings::isUuid('9f8f8f8f-1234-4321-abcd-1234567890ab'); // true` |
| `isUlid($value)` | 驗證是否為 ULID 格式。 | `Strings::isUlid('01ARZ3NDEKTSV4RRFFQ69G5FAV'); // true` |
| `plural($value, $count = 2)` | 把常見英文單字轉成複數（只是手動挑選的小規則集，**不是**完整的 inflector）。 | `Strings::plural('box'); // 'boxes'` |
| `singular($value)` | 把常見英文單字轉成單數（跟 `plural` 有同樣的限制）。 | `Strings::singular('boxes'); // 'box'` |
| `rand($length)` | 產生隨機英數字字串。 | `strlen(Strings::rand(8)); // 8` |
| `getClassBaseName($class)` | 取得物件或完整類別名稱（FQCN）的短類別名稱。 | `Strings::getClassBaseName('Wilkques\Helpers\Objects'); // 'Objects'` |

### `Wilkques\Helpers\Objects`

支援**混合陣列與物件**（含物件屬性存取）的 dot notation 取值/設值，是 `data_get()` / `data_set()` 全域函式背後的實作。

| 方法 | 說明 | 範例 |
| --- | --- | --- |
| `get($target, $key, $default = null)` | 用 dot notation 從陣列或物件取值。支援 `{first}`/`{last}` 指令與 `\*`/`\{first}`/`\{last}` 跳脫語法。 | `Objects::get($config, 'db.host'); // 'localhost'` |
| `set(&$target, $key, $value, $overwrite = true)` | 用 dot notation 對陣列或物件設值。 | `Objects::set($config, 'db.port', 5432); // $config->db['port'] === 5432` |
| `exists($array, $key)` | 檢查 key 是否存在（陣列或 `ArrayAccess`）。 | `Objects::exists(['name' => 'Wilkques'], 'name'); // true` |
| `accessible($value)` | 判斷值是否為陣列可存取（array-accessible）。 | `Objects::accessible([]); // true` |
| `value($value, ...$args)` | 解析一個值，如果是 `Closure` 就呼叫它。 | `Objects::value(function () { return 'bar'; }); // 'bar'` |

### `Wilkques\Helpers\Collections`

| 方法 | 說明 | 範例 |
| --- | --- | --- |
| `new Collections($items = [])` | 從陣列、JSON 字串、另一個 `Collections`、`Traversable`、或 `JsonSerializable` 建立。 | `(new Collections([1, 2]))->all(); // [1, 2]` |
| `Collections::make($items = [])` | 跟 `new Collections()` 一樣，靜態寫法。 | `Collections::make([1, 2])->all(); // [1, 2]` |
| `all()` | 取得底層的資料陣列。 | `collect([1, 2, 3])->all(); // [1, 2, 3]` |
| `toArray()` | 遞迴轉成純陣列。 | `collect(['a' => collect([1, 2])])->toArray(); // ['a' => [1, 2]]` |
| `toJson($options = 0)` | 把項目編碼成 JSON。 | `collect(['a' => 1])->toJson(); // '{"a":1}'` |
| `count()` | 項目數量。 | `collect([1, 2, 3])->count(); // 3` |
| `isEmpty()` | 判斷集合是否沒有項目。 | `collect([])->isEmpty(); // true` |
| `isNotEmpty()` | 判斷集合是否有項目。 | `collect([1])->isNotEmpty(); // true` |
| `map($callback)` | 轉換每個項目（回傳新的 collection）。 | `collect([1, 2])->map(function ($n) { return $n * 10; })->all(); // [10, 20]` |
| `mapWithKeys($callback)` | 一次完成轉換與重新設定 key。 | `collect([['id' => 1, 'name' => 'a']])->mapWithKeys(function ($i) { return [$i['id'] => $i['name']]; })->all(); // [1 => 'a']` |
| `filter($callback = null)` | 保留符合 callback 條件的項目。 | `collect([1, 2, 3, 4])->filter(function ($n) { return $n % 2 === 0; })->all(); // [1 => 2, 3 => 4]` |
| `reject($callback)` | 丟掉符合 callback 條件的項目（`filter` 的相反）。 | `collect([1, 2, 3, 4])->reject(function ($n) { return $n % 2 === 0; })->all(); // [0 => 1, 2 => 3]` |
| `whereNotNull()` | 丟掉 `null` 項目。 | `collect([1, null, 2])->whereNotNull()->all(); // [0 => 1, 2 => 2]` |
| `pluck($value, $key = null)` | 取出某個欄位。 | `collect([['id' => 1, 'name' => 'a']])->pluck('name', 'id')->all(); // [1 => 'a']` |
| `values()` | 重新依序索引。 | `collect(['a' => 1, 'b' => 2])->values()->all(); // [1, 2]` |
| `keys()` | 取出所有 key。 | `collect(['a' => 1, 'b' => 2])->keys()->all(); // ['a', 'b']` |
| `flip()` | 互換 key 與 value。 | `collect(['a' => 1])->flip()->all(); // [1 => 'a']` |
| `only($keys)` | 只保留指定的 key。 | `collect(['a' => 1, 'b' => 2])->only('a')->all(); // ['a' => 1]` |
| `except($keys)` | 移除指定的 key。 | `collect(['a' => 1, 'b' => 2])->except('a')->all(); // ['b' => 2]` |
| `merge($items)` | 跟另一個陣列／collection 合併。 | `collect(['a' => 1])->merge(['b' => 2])->all(); // ['a' => 1, 'b' => 2]` |
| `unique($key = null)` | 去除重複值，可選擇依 key／callback 判斷。 | `collect([1, 2, 2, 3])->unique()->all(); // [0 => 1, 1 => 2, 3 => 3]` |
| `flatten($depth = INF)` | 攤平巢狀陣列。 | `collect(['a' => 1, [2, 3]])->flatten()->all(); // [1, 2, 3]` |
| `collapse()` | 把「collection 的陣列」合併成單一層。 | `collect([[1, 2], [3, 4]])->collapse()->all(); // [1, 2, 3, 4]` |
| `sort($callback = null)` | 依值、callback、或 dot-notation key 排序。 | `collect([3, 1, 2])->sort()->all(); // [1 => 1, 2 => 2, 0 => 3]` |
| `sortDesc($callback = null)` | 由大到小排序。 | `collect([1, 3, 2])->sortDesc()->all(); // [1 => 3, 2 => 2, 0 => 1]` |
| `sortBy($callback, $options = SORT_REGULAR, $descending = false)` | 依單一 key/callback 排序，或傳入 `[key, 'asc'\|'desc']` 陣列做多欄位排序（依序 tie-break）。`$options` 可用一般的 `SORT_*` 旗標。 | `collect([['age' => 30, 'name' => 'b'], ['age' => 30, 'name' => 'a'], ['age' => 20, 'name' => 'c']])->sortBy([['age', 'desc'], 'name'])->pluck('name')->all(); // ['a', 'b', 'c']` |
| `sortByDesc($callback, $options = SORT_REGULAR)` | 降冪排序，接受跟 `sortBy()` 一樣的單一/多欄位陣列參數。 | `collect([['age' => 30], ['age' => 20]])->sortByDesc('age')->pluck('age')->all(); // [30, 20]` |
| `chunk($size)` | 拆成一個「裝著多個 collection」的 collection。 | `collect([1, 2, 3, 4, 5])->chunk(2)->count(); // 3（大小分別是 2、2、1）` |
| `groupBy($groupBy, $preserveKeys = false)` | 依 callback 或 dot-notation key 分組，或傳入陣列做多層巢狀分組。單一 retriever 也可以回傳陣列，讓一筆資料同時歸入多個分組。`$preserveKeys` 保留原始 key，不重新編號。 | `collect([['type' => 'a', 'sub' => 'x'], ['type' => 'a', 'sub' => 'y'], ['type' => 'b', 'sub' => 'x']])->groupBy(['type', 'sub'])->get('a')->get('x')->count(); // 1` |
| `keyBy($keyBy)` | 依 callback 或 dot-notation key 重新設定 key。 | `collect([['id' => 1, 'name' => 'a']])->keyBy('id')->get(1); // ['id' => 1, 'name' => 'a']` |
| `implode($value, $glue = null)` | 串接純量項目，或先 pluck 再串接（陣列的陣列時）。 | `collect(['a', 'b', 'c'])->implode(','); // 'a,b,c'` |
| `reduce($callback, $initial = null)` | 歸約成單一值。 | `collect([1, 2, 3])->reduce(function ($c, $n) { return $c + $n; }, 0); // 6` |
| `each($callback)` | 逐一迭代；callback 回傳 `false` 可提前中止。 | `collect([1, 2, 3])->each(function ($n) { echo $n; }); // 印出 123` |
| `first($callback = null, $default = null)` | 取第一個項目，可選擇符合 callback 條件。 | `collect([1, 2, 3])->first(); // 1` |
| `last($callback = null, $default = null)` | 取最後一個項目，可選擇符合 callback 條件。 | `collect([1, 2, 3])->last(); // 3` |
| `contains($key, $value = null)` | 檢查是否包含某個值、符合某個 callback、或某組 key/value。 | `collect([1, 2, 3])->contains(2); // true` |
| `get($key, $default = null)` | 用 dot notation 取值。 | `collect(['a' => 1])->get('a'); // 1` |
| `has($key)` | 用 dot notation 檢查 key 是否存在。 | `collect(['a' => 1])->has('a'); // true` |
| `set($key, $value)` | 設值（會修改當前集合，回傳 `$this`）。 | `$c->set('b', 2); // $c->get('b') === 2` |
| `put($key, $value)` | `set()` 的別名。 | `$c->put('a', 1); // $c->get('a') === 1` |
| `forget($keys)` | 移除一個或多個 key（會修改當前集合）。 | `$c->forget('a'); // $c 不再有 'a' 這個 key` |
| `pull($key, $default = null)` | 取值後同時移除（會修改當前集合）。 | `$c->pull('a'); // 回傳該值，並移除 'a'` |
| `push($value)` | 新增一個值到最後面（會修改當前集合）。 | `collect([1, 2])->push(3)->all(); // [1, 2, 3]` |
| `prepend($value, $key = null)` | 新增一個值到最前面（會修改當前集合）。 | `collect([1, 2])->prepend(0)->all(); // [0, 1, 2]` |

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
