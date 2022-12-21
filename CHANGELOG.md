# Changelog: Stdlib

> version: $Id$ ($Date$)

## History

### 0.4.0 @2022 Dec 21
 - new: `Json` JSON en/decoder
 - update: `Options` switch to use `Json`
 - update: `Options` improvements to phpdoc

### 0.3.1 @2022 Dec 10

 - fix: `Options::offsetExists` Returned false if value was null even though offset exists

### 0.3.0 @2022 Dec 10

 - new: `Options::empty` Test if empty
 - new: `Options::prev` Rewinds the internal pointer by 1
 - new: `Options::modify` Merge an array but only updates existing keys, ignoring unmatched keys
 - new: `Options::complete` Merge an array but only adds missing keys, leaving existing keys unmodified
 - new: `Inflector::breakOnUppercase` Break on uppercase letters
 - new: `Str::pad` Pad to a certain length with character
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
