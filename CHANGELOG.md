# Changelog: Stdlib

> version: $Id$ ($Date$)

## History

### current: 0.1.6 (2022 Aug xx)

- New: `ArrayPathAccessTrait` allows read/write array data using strings, read: "users/bob/age", write: "users/bob/age=30"
- New: `Highlight::render` applies highlight to **$code** parameter
- Update: `Options` added access to data via string paths
- New: `Options::toXML` export data as XML string
- New: `Options::keys` returns iterable of keys
- New: `Options::values` returns iterable of values
- Update: `InvalidPropertyException` now able to specify `Object`
- New: `Inflector::hyphenate` @see `Inflector::underscore`
- New: `ArrayUtil::stringPath` single string command for read/write actions
- New: `Converters::ArrayToXML` trait to convert an array to xml

### 0.1.5 @2022 Aug 04

 - Str: added method basename
 - FileInfo: moved to `inanepain/file` with ns `\Inane\File`
