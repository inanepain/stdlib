# Changelog: Stdlib

> version: $Id$ ($Date$)

## History

### 0.3.0-dev @2022 Oct xx

- new: `Options::empty` Test if empty
- todo: `...able` more able interfaces
- todo: `ArrayKit` test more functions and add @method tags for them
- todo: `ArrayKit` move to lib base
- todo: `String` move to lib base

### 0.2.0 @2022 Oct 19

- new: `ArrayPathAccessTrait` allows read/write array data using strings, read: "users/bob/age", write: "users/bob/age=30"
- new: `Highlight::render` applies highlight to **$code** parameter
- new: `Options::toXML` export data as XML string
- new: `Options::keys` returns iterable of keys
- new: `Options::values` returns iterable of values
- new: `Inflector::hyphenate` @see `Inflector::underscore`
- new: `ArrayUtil::stringPath` single string command for read/write actions
- new: `ArrayKit` wrapper for array_... functions
- new: `Converters::ArrayToXML` trait to convert an array to xml
- new: `StringUtility` some string processing tools
- new: `{Array,JSON,XML}able` interfaces for to{Array,JSON,XML} export methods
- update: `Options` added access to data via string paths
- update: `InvalidPropertyException` now able to specify `Object`
- update: `Exceptions` changed inheritance and some error codes

### 0.1.5 @2022 Aug 04

 - Str: added method basename
 - FileInfo: moved to `inanepain/file` with ns `\Inane\File`
