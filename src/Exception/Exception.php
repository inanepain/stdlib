<?php
/**
 * InaneTools
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * PHP version 8
 *
 * @author Philip Michael Raab <philip@inane.co.za>
 * @package Inane\Exception
 *
 * @license MIT
 * @license https://inane.co.za/license/MIT
 *
 * @copyright 2015-2021 Philip Michael Raab <philip@inane.co.za>
 */
declare(strict_types=1);

namespace Inane\Stdlib\Exception;

use Exception as SystemException;
use Throwable;

/**
 * Exception
 *
 * @package Inane\Exception
 *
 * @version 0.3.0
 */
class Exception extends SystemException implements ExceptionInterface {
    /**
     * Custom construct template
     *
     * @param string $message error description
     * @param int $code unique identifier
     * @param \Throwable|null $previous error if any
     *
     * @return void
     */
    // public function __construct($message = '', $code = 0, Throwable $previous = null) {
    //     // some code

    //     // make sure everything is assigned properly, call parent construct
    //     parent::__construct($message, $code, $previous);
    // }

    /**
     * toString
     *
     * @return string error as string
     */
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    /**
     * Dump exception
     *
     * Uses Dumper to log exception
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function dump(): void {
        \Inane\Debug\Dumper::dump("{$this->code}: {$this->message}", 'Exception');
    }
}
