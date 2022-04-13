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
 * Exception that represents error in the program logic. This kind of exception should lead directly to a fix in your code.
 *
 * @package Inane\Exception
 * @implements \Inane\Stdlib\Exception\ExceptionInterface
 * @version 0.2.0
 */
class LogicException extends \LogicException implements ExceptionInterface {}
