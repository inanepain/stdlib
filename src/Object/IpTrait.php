<?php

/**
 * Inane: Stdlib
 *
 * Common classes that cover a wide range of cases that are used throughout the inanepain libraries.
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
