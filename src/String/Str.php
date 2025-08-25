<?php

/**
 * Inane: Stdlib
 *
 * Common classes, tools and utilities used throughout the inanepain libraries.
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.4
 *
 * @author Philip Michael Raab<philip@cathedral.co.za>
 * @package inanepain\stdlib
 * @category stdlib
 *
 * @license UNLICENSE
 * @license https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types=1);

namespace Inane\Stdlib\String;

use Stringable;

use function array_merge;
use function basename;
use function count;
use function in_array;
use function is_null;
use function lcfirst;
use function mt_rand;
use function rand;
use function str_contains;
use function str_pad;
use function str_replace;
use function strlen;
use function strrpos;
use function strtolower;
use function strtoupper;
use function strval;
use function substr_replace;
use function trim;
use function ucwords;
use const false;
use const null;
use const STR_PAD_RIGHT;
use const true;

use Inane\Stdlib\{
    Exception\InvalidPropertyException,
    Object\MagicPropertyTrait,
    ArrayObject,
    Highlight
};

/**
 * Str
 *
 * @property-read public length
 * @property public string
 *
 * @version 0.7.2
 */
class Str implements Stringable {
    use MagicPropertyTrait;

    /**
     * Capitalisation
     */
    protected Capitalisation $_case = Capitalisation::Ignore;

    /**
     * Storage buffer
     *
     * A shared buffer for storing values
     *
     * @var \Inane\Stdlib\ArrayObject
     */
    protected static $buffer;

    /**
     * The id used to access the buffer.
     */
    private string $id;

    /**
     * Creates instance of Str object
     *
     * @param string $string
     */
    public function __construct(
        /**
         * Initial string value
         */
        protected string $value = ''
    ) {
    }

    /**
     * Clean up memory buffer
     */
    public function __destruct() {
        if (isset($this->id) && false) unset(static::$buffer[$this->id]);
    }

    /**
     * Create from string
     *
     * @since 0.5.0
     *
     * @param string|int|float $string string
     *
     * @return static
     */
    public static function from(string|int|float $string): static {
        return new static("$string");
    }

    /**
     * Set State
     *
     * @param array $data
     *
     * @return void
     */
    public static function __set_state(array $data): static {
        $obj = new static($data['_str']);
        $obj->_case = Capitalisation::tryFrom($data['_case']);

        return $obj;
    }

    /**
     * magic method: _get
     *
     * @param string $property
     *
     * @return mixed
     *
     * @throws \Inane\Stdlib\Exception\InvalidPropertyException when requested property does not exist
     */
    public function __get($property) {
        if (!in_array($property, ['length', 'string']))
            throw new InvalidPropertyException("Invalid Property:\n\tStr has no property: {$property}");

        $methods = [
            'length' => 'length',
            'string' => 'getString'
        ];

        return $this->{$methods[$property]}();
    }

    /**
     * magic method: _set
     *
     * @param string $property
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws \Inane\Stdlib\Exception\InvalidPropertyException when requested property does not exist
     */
    public function __set($property, $value) {
        if (!in_array($property, ['string']))
            throw new InvalidPropertyException("Invalid Property:\n\tStr has no property: {$property}");

        $methods = [
            'length' => 'length',
            'string' => 'setString'
        ];

        $this->{$methods[$property]}($value);

        return $this;

        $method = $this->parseMethodName($property, 'set');
        $this->$method($value);
    }

    /**
     * Echoing the Str object print out the string
     *
     * @return string
     */
    public function __toString(): string {
        return $this->value;
    }

    /**
     * basename
     *
     * @since 0.6.0
     *
     * @param string $suffix to remove
     *
     * @return \Inane\Stdlib\String\Str
     */
    public function baseName(string $suffix = ''): Str {
        $this->value = basename($this->value, $suffix);

        return $this;
    }

    /**
     * Get: basename
     *
     * GET Methods: don't effect value of Str just return the result of method on Str
     *
     * @since 0.6.0
     *
     * @param string $suffix to remove
     *
     * @return string
     */
    public function getBaseName(string $suffix = ''): string {
        return basename($this->value, $suffix);
    }

    /**
     * Fetches storage from shared buffer.
     *
     * @return ArrayObject storage memory
     */
    protected function storage(): ArrayObject {
        if (!isset($this->id)) {
            if (!isset(static::$buffer)) static::$buffer = new ArrayObject();
            static::$buffer[($this->id = uniqid())] = new ArrayObject();
        }

        return static::$buffer[$this->id];
    }

    /**
     * Gets the string at buffer $id or latest
     *
     * @param int|null $id buffer to use for string or latest if null
     *
     * @return static
     */
    public function bufferAt(?int $id = null): string {
        if (is_null($id)) $id = count($this->storage()) - 1;
        return $id >= 0 ? $this->storage()[$id] : '';
    }

    /**
     * Saves current value to buffer then replaces value with $string
     *
     * @return static with new value
     */
    public function bufferAndReplace(string $string): static {
        $this->buffer();
        $this->value = $string;

        return $this;
    }

    /**
     * Copies current value to buffer
     *
     * @return int buffer id for stored value
     */
    public function buffer(): int {
        $id = count($this->storage());
        $this->storage()[$id] = $this->value;

        return $id;
    }

    /**
     * Restores value to string in buffer $id or latest
     *
     * @param int|null $id buffer to use for string or latest if null
     *
     * @return static
     */
    public function restore(?int $id = null): static {
        $this->value = $this->bufferAt($id);

        return $this;
    }

    /**
     * Appends string in buffer $id or most recent buffer if not
     *
     * @param int|null $id buffer to use for string or latest if null
     *
     * @return static
     */
    public function appendBuffer(?int $id = null): static {
        $this->value .= $this->bufferAt($id);

        return $this;
    }

    /**
     * Prepends string in buffer $id or most recent buffer if not
     *
     * @param int|null $id buffer to use for string or latest if null
     *
     * @return static
     */
    public function prependBuffer(?int $id = null): static {
        $this->value = $this->bufferAt($id) . $this->value;

        return $this;
    }

    /**
     * Append str to Str
     *
     * @param string $str
     *
     * @return \Inane\Stdlib\String\Str
     */
    public function append(string $str): Str {
        $this->value .= $str;

        return $this;
    }

    /**
     * Check if Str contains needle
     *
     * @param string $needle
     *
     * @return bool
     */
    public function contains(string $needle): bool {
        return self::str_contains($needle, $this->value);
    }

    /**
     * getString
     *
     * @return string
     */
    public function getString(): string {
        return $this->value;
    }

    /**
     * length of str
     *
     * @return int
     */
    public function length(): int {
        return strlen($this->value);
    }

    /**
     * Prepend str to Str
     *
     * @param string $str
     *
     * @return Str
     */
    public function prepend(string $str): Str {
        $this->value = "{$str}{$this->value}";

        return $this;
    }

    /**
     * Replaces last match of search with replace
     *
     * @param string $search
     * @param string $replace
     *
     * @return \Inane\Stdlib\String\Str
     */
    public function replaceLast(string $search, string $replace): Str {
        $this->value = self::str_replace_last($search, $replace, $this->value);

        return $this;
    }

    /**
     * Replaces text from beginning to end
     *  - if $limit not null, only that amount of matches will be replaces
     *
     * @param string $search
     * @param string $replace
     * @param null|int $limit
     *
     * @return \Inane\Stdlib\String\Str
     */
    public function replace(string $search, string $replace, ?int $limit = null): Str {
        $this->value = Str::str_replace($search, $replace, $this->value, $limit);

        return $this;
    }

    /**
     * Set the string value
     *
     * @param string $string
     *
     * @return \Inane\Stdlib\String\Str
     */
    public function setString(string $string): Str {
        $this->value = $string;

        return $this;
    }

    /**
     * Pad to a certain length with character
     *
     * @since 0.7.0
     *
     * @param int $length If the value of length is negative, less than, or equal to the length of the input string, no padding takes place.
     * @param string $padString May be truncated if the required number of padding characters can't be evenly divided by the padString's length.
     * @param int $type Optional argument type can be STR_PAD_RIGHT, STR_PAD_LEFT, or STR_PAD_BOTH. If type is not specified it is assumed to be STR_PAD_RIGHT.
     *
     * @return \Inane\Stdlib\String\Str
     */
    public function pad(int $length, string $padString = ' ', int $type = STR_PAD_RIGHT): Str {
        $this->value = str_pad($this->value, $length, $padString, $type);

        return $this;
    }

    /**
     * Check if haystack contains needle
     *
     * @param string $needle
     * @param string $haystack
     *
     * @return bool
     */
    public static function str_contains(string $needle, string $haystack): bool {
        return str_contains($haystack, $needle);
    }

    /**
     * Replaces text from beginning to end
     *  - if $limit not null, only that amount of matches will be replaces
     *
     * @param string $search
     * @param string $replace
     * @param string $str
     * @param null|int $limit
     *
     * @return string
     */
    public static function str_replace(string $search, string $replace, string $str, ?int $limit = null): string {
        if (!is_null($limit)) {
            $from = '/' . preg_quote($search, '/') . '/';
            $str = preg_replace($from, $replace, $str, $limit);
        } else $str = str_replace($search, $replace, $str);

        return $str;
    }

    /**
     * Replaces last match of search with replace in str
     *
     * @param string $search
     * @param string $replace
     * @param string $str
     *
     * @return string
     */
    public static function str_replace_last(string $search, string $replace, string $str): string {
        if (($pos = strrpos($str, $search)) !== false) {
            $search_length = strlen($search);
            $str = substr_replace($str, $replace, $pos, $search_length);
        }

        return $str;
    }

    /**
     * Changes the case of $string to $case and optionally removes spaces
     *
     * @param string $string
     * @param Capitalisation $case
     * @param bool $removeSpaces
     *
     * @return string
     */
    public static function str_to_case(string $string, Capitalisation $case, bool $removeSpaces = false): string {
        // $RaNDom = function ($text) {
        //     for ($i = 0, $c = strlen($text); $i < $c; $i++) {
        //         $text[$i] = (rand(0, 100) > 50
        //             ? strtoupper($text[$i])
        //             : strtolower($text[$i]));
        //     }
        //     return $text;
        // };

        switch ($case) {
            case Capitalisation::UPPERCASE:
                $string = strtoupper($string);
                break;

            case Capitalisation::lowercase:
                $string = strtolower($string);
                break;

            case Capitalisation::camelCase:
                $string = lcfirst(ucwords(strtolower($string)));
                break;

            case Capitalisation::StudlyCaps:
                $string = ucwords(strtolower($string));
                break;

            case Capitalisation::RaNDom:
                for ($i = 0, $c = strlen($string); $i < $c; $i++) {
                    $string[$i] = (rand(0, 100) > 50
                        ? strtoupper($string[$i])
                        : strtolower($string[$i]));
                }
                break;

            default:
                break;
        }

        if ($removeSpaces) $string = str_replace(' ', '', $string);

        return $string;
    }

    /**
     * Create Str with $length random characters
     *
     * @param int $length
     * @return Str
     */
    public static function stringWithRandomCharacters(int $length = 6): Str {
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;

        $str = new self();
        while ($str->length < $length) {
            $rand = mt_rand(0, $max);
            $str->append(strval($characters[$rand]));
        }

        return $str;
    }

    /**
     * Changes the case of Str to $case and optionally removes spaces
     *
     * @param Capitalisation $case
     * @param bool $removeSpaces
     *
     * @return Str
     */
    public function toCase(Capitalisation $case, bool $removeSpaces = false): Str {
        $this->value = static::str_to_case($this->value, $case, $removeSpaces);
        $this->_case = $case;

        return $this;
    }

    /**
     * Trim chars from beginning and end of string default chars ' ,:-./\\`";'
     *
     * @param string $chars to trim
     * @return Str
     */
    public function trim(string $chars = ' ,:-./\\`";'): Str {
        $this->value = trim($this->value, $chars);

        return $this;
    }

    /**
     * highlight str
     *
     * @param \Inane\Stdlib\Highlight $highlight (default, php2, html)
     * @param bool $removeOpenTag remove the <?php that is added
     *
     * @return Str
     */
    public function highlight(Highlight $highlight = null, bool $removeOpenTag = true): Str {
        if (is_null($highlight)) $highlight = Highlight::DEFAULT;

        $highlight->apply();

        $text = trim($this->value);
        $text = highlight_string("<?php\n" . $text, true);
        if ($removeOpenTag) $text = str_replace("&lt;?php<br />", '', $text);

        $text = highlight_string('<?php ' . $text, true);  // highlight_string() requires opening PHP tag or otherwise it will not colorize the text
        $text = trim($text);
        $text = preg_replace("|^\\<code\\>\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>|", '', $text, 1);  // remove prefix
        $text = preg_replace("|\\</code\\>\$|", '', $text, 1);  // remove suffix 1
        $text = trim($text);  // remove line breaks
        $text = preg_replace("|\\</span\\>\$|", '', $text, 1);  // remove suffix 2
        $text = trim($text);  // remove line breaks
        $this->value = preg_replace("|^(\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>)(&lt;\\?php&nbsp;)(.*?)(\\</span\\>)|", "\$1\$3\$4", $text);  // remove custom added "<?php "

        return $this;
    }

    /**
     * highlight text
     *
     * @param string $text
     * @param \Inane\Stdlib\Highlight $highlight (default, php, php2, html)
     *
     * @return Str
     */
    public static function highlightText(string $text, ?Highlight $highlight = null): Str {
        if (is_null($highlight)) $highlight = Highlight::DEFAULT;

        $new = new static($text);
        return $new->highlight($highlight);
    }

    /**
     * Clones Str
     *
     * Returns a clone of Str letting you go on and leave the original untouched.
     *
     * @return static
     */
    public function duplicate(): static {
        return clone ($this);
    }
}
