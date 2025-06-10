<?php

/**
 * Inane: Stdlib
 *
 * Inane Standard Library
 *
 * PHP version 8.1
 *
 * @author Philip Michael Raab<philip@cathedral.co.za>
 * @package Inane\Stdlib
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\Stdlib\Object;

use function array_key_exists;

/**
 * IpTrait
 *
 * Client IP address
 *
 * @since 0.4.6
 *
 * @version 1.0.1
 *
 * @package Inane\Stdlib\Object
 */
trait IpTrait {

    /**
     * IP Address
     */
    protected string $ipAddress;

    /**
     * Get IP Address
     *
     * @return string the client ip
     */
    public function getIp(): ?string {
        if (!isset($this->ipAddress)) {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) $this->ipAddress = $_SERVER['HTTP_CLIENT_IP']; // ip from share internet
            elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) $this->ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR']; // ip pass from proxy
            else if (array_key_exists('REMOTE_ADDR', $_SERVER)) $this->ipAddress = $_SERVER['REMOTE_ADDR'];
            else $this->ipAddress = '127.0.0.1';
        }
        return $this->ipAddress;
    }
}
