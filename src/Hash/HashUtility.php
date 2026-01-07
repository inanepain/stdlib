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
 * @author   Philip Michael Raab<philip@cathedral.co.za>
 * @package  inanepain\stdlib
 * @category stdlib
 *
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types = 1);

namespace Inane\Stdlib\Hash;

use Inane\Stdlib\Exception\ValueError;
use function hash;
use function str_replace;
use function strtolower;

/**
 * Utility class for identifying potential hash types based on the format, length, or prefixes of provided hash strings.
 */
class HashUtility {
    /**
     * Identifies the potential hash types of the given hash string based on its format and length.
     *
     * @param string $hash     The hash string that needs to be identified, expected to be a hexadecimal or encoded string.
     * @param bool   $extended A flag indicating whether to include extended hash types in the identification process.
     *
     * @return HashType[] An array of identified hash types.
     */
    public static function identifyHash(string $hash, bool $extended = false): array {
        $types = [];

        if (preg_match('/^[a-f0-9]{32}$/i', $hash)) {
            $types = array_merge($types, HashType::MD5->getIndividualCases());
            if ($extended) {
                $types = array_merge($types, HashType::HEX_32->getIndividualCases());
            }
        }
        if ($extended) {
            if (preg_match('/^[a-f0-9]{40}$/i', $hash)) {
                $types = array_merge($types, HashType::SHA_1->getIndividualCases());
                if ($extended) {
                    $types = array_merge($types, HashType::HEX_40->getIndividualCases());
                }
            }
            if (preg_match('/^[a-f0-9]{48}$/i', $hash)) {
                $types = array_merge($types, HashType::HEX_48->getIndividualCases());
            }
            if (preg_match('/^[a-f0-9]{56}$/i', $hash)) {
                $types = array_merge($types, HashType::HEX_56->getIndividualCases());
            }
        }
        if (preg_match('/^[a-f0-9]{64}$/i', $hash)) {
            $types = array_merge($types, HashType::SHA_256->getIndividualCases());
            if ($extended) {
                $types = array_merge($types, HashType::HEX_64->getIndividualCases());
            }
        }
        if (preg_match('/^[a-f0-9]{80}$/i', $hash)) {
            $types = array_merge($types, HashType::HEX_80->getIndividualCases());
        }
        if (preg_match('/^[a-f0-9]{96}$/i', $hash)) {
            $types = array_merge($types, HashType::SHA_384->getIndividualCases());
            if ($extended) {
                $types = array_merge($types, HashType::HEX_96->getIndividualCases());
            }
        }
        if (preg_match('/^[a-f0-9]{128}$/i', $hash)) {
            $types = array_merge($types, HashType::SHA_512->getIndividualCases());
            if ($extended) {
                $types = array_merge($types, HashType::HEX_128->getIndividualCases());
            }
        }
        if (str_starts_with($hash, '$2y$') || str_starts_with($hash, '$2a$')) {
            $types = array_merge($types, HashType::Bcrypt->getIndividualCases());
        }
        if (str_starts_with($hash, '$1$')) {
            $types = array_merge($types, HashType::MD5_crypt->getIndividualCases());
        }
        if (str_starts_with($hash, '$5$')) {
            $types = array_merge($types, HashType::SHA_256_crypt->getIndividualCases());
        }
        if (str_starts_with($hash, '$6$')) {
            $types = array_merge($types, HashType::SHA_512_crypt->getIndividualCases());
        }
        if (str_starts_with($hash, '$argon2')) {
            $types = array_merge($types, HashType::Argon2->getIndividualCases());
        }
        if (preg_match('/^\$pbkdf2-(sha[0-9]+)\$/i', $hash) || preg_match('/^pbkdf2[_-]?sha/i', $hash)) {
            $types = array_merge($types, HashType::PBKDF2->getIndividualCases());
        }
        if (str_starts_with($hash, '$scrypt')) {
            $types = array_merge($types, HashType::Scrypt->getIndividualCases());
        }

        return array_unique($types, SORT_REGULAR);
    }

    /**
     * Hashes $data
     *
     * @param string $data string to hash
     * @param HashType|string $hashType hash type to use
     *
     * @return string hash of $data
     */
    public static function hash(string $data, HashType|string $hashType = HashType::MD5): string {
        if ($hashType instanceof HashType) $hashType = $hashType->description();
        $type = strtolower($hashType);
//        $type = strstr($type . ',', ',', true);
        $type = str_replace('-', '', $type);

        try {
            $hash = hash($type, $data, false);
        } catch (\ValueError $e) {
            throw new ValueError("Invalid hash type: $type", $e->getCode(), $e);
        }

        return $hash;
    }

    public static function listAlgorythems(): array {
        return hash_algos();
    }
}
