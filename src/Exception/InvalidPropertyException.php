<?php
/**
 * This file is part of the InaneTools package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Philip Michael Raab <philip@inane.co.za>
 * @package Inane\Exception
 *
 * @license MIT
 * @license https://inane.co.za/license/MIT
 *
 * @copyright 2015-2019 Philip Michael Raab <philip@inane.co.za>
 */

namespace Inane\Stdlib\Exception;

/**
 * Magic GET/SET reject property
 *
 * @package Inane\Exception
 * @implements \Inane\Stdlib\Exception\ExceptionInterface
 * @version 0.2.0
 */
class InvalidPropertyException extends \LogicException implements ExceptionInterface {
    protected $message = 'Property exception: `magic_property_properties` property invalid';   // exception message
    protected $code = 200;                        // user defined exception code
    protected string $file;                            // source filename of exception
    protected int $line;                            // source line of exception

    /**
     * __construct
     *
     * @param null|string $message
     * @param int $code
     * @param Exception|null $previous
     * @return void
     */
    public function __construct(?string $message = null, $code = 0, Exception $previous = null) {
        if ($previous === null) $this->message = str_replace('magic_property_properties', 'Object', $this->message);
        $message = $this->message . ($message ? ': ' . $message : '');
        $code = $this->code + $code;

        $debugBacktrace = array_pop(debug_backtrace(0, 2));
        $this->file = $debugBacktrace['file'];
        $this->line = $debugBacktrace['line'];

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    /**
     * magic method: __toString
     *
     * @return string
     */
    public function __toString() {
        return __CLASS__ . ":\n [{$this->code}]: {$this->message}";
    }
}
