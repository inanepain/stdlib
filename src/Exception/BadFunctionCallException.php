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
 * Exception thrown if a callback refers to an undefined function or if some arguments are missing.
 *
 * @package Inane\Exception
 * @implements \Inane\Stdlib\Exception\ExceptionInterface
 * @version 0.2.0
 */
class BadFunctionCallException extends \BadFunctionCallException implements ExceptionInterface {}
