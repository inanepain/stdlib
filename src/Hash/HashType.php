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

use Inane\Stdlib\Enum\CoreEnumInterface;

/**
 * Enum of supported hash types.
 */
enum HashType: int implements CoreEnumInterface {
    case MD5 = 1 << 0;
    case HAVAL_128 = 1 << 1;
    case Snefru_128 = 1 << 2;
    case RIPEMD_128 = 1 << 3;

    case SHA_1 = 1 << 4;
    case RIPEMD_160 = 1 << 5;
    case HAVAL_160 = 1 << 6;

    case Tiger192 = 1 << 7;
    case Tiger_192 = 1 << 8;
    case HAVAL_192 = 1 << 9;

    case HAVAL_224 = 1 << 10;
    case RIPEMD_224 = 1 << 11;

    case SHA_256 = 1 << 12;
    case SHA3_256 = 1 << 13;
    case BLAKE2s_256 = 1 << 14;
    case HAVAL_256 = 1 << 15;
    case Snefru_256 = 1 << 16;
    case Tiger_256 = 1 << 17;
    case RIPEMD_256 = 1 << 18;
    /**
     * Represents the RIPEMD-320 hashing algorithm.
     *
     * Would also do HASH_80
     */
    case RIPEMD_320 = 1 << 19;

    case SHA_384 = 1 << 20;
    case SHA3_384 = 1 << 21;

    case SHA_512 = 1 << 22;
    case SHA3_512 = 1 << 23;
    case BLAKE2b_512 = 1 << 24;
    case Whirlpool = 1 << 25;

    case Bcrypt = 1 << 26;
    case MD5_crypt = 1 << 27;
    case SHA_256_crypt = 1 << 28;
    case SHA_512_crypt = 1 << 29;
    case Argon2 = 1 << 30;
    case PBKDF2 = 1 << 31;
    case Scrypt = 1 << 32;

    #region Groups
    case HEX_32 = (1 << 0) | (1 << 1) | (1 << 2) | (1 << 3);
    case HEX_40 = (1 << 4) | (1 << 5) | (1 << 6);
    case HEX_48 = (1 << 7) | (1 << 8) | (1 << 9);
    case HEX_56 = (1 << 10) | (1 << 11);
    case HEX_64 = (1 << 12) | (1 << 13) | (1 << 14) | (1 << 15) | (1 << 16) | (1 << 17) | (1 << 18);
//    case HEX_80 = (1 << 19);
    case HEX_96 = (1 << 20) | (1 << 21);
    case HEX_128 = (1 << 22) | (1 << 23) | (1 << 24) | (1 << 25);
    case CRYPT = (1 << 26) | (1 << 27) | (1 << 28) | (1 << 29) | (1 << 30) | (1 << 31) | (1 << 32);
    #endregion Groups

    /**
     * Get the string representation of the hash type.
     *
     * @return string
     */
    public function description(): string {
        return match ($this) {
            self::MD5 => 'MD5',
            self::HAVAL_128 => 'HAVAL-128',
            self::Snefru_128 => 'Snefru-128',
            self::RIPEMD_128 => 'RIPEMD-128',
            self::SHA_1 => 'SHA-1',
            self::RIPEMD_160 => 'RIPEMD-160',
            self::HAVAL_160 => 'HAVAL-160',
            self::Tiger192 => 'Tiger192',
            self::Tiger_192 => 'Tiger/192',
            self::HAVAL_192 => 'HAVAL-192',
            self::HAVAL_224 => 'HAVAL-224',
            self::RIPEMD_224 => 'RIPEMD-224',
            self::SHA_256 => 'SHA-256',
            self::SHA3_256 => 'SHA3-256',
            self::BLAKE2s_256 => 'BLAKE2s-256',
            self::HAVAL_256 => 'HAVAL-256',
            self::Snefru_256 => 'Snefru-256',
            self::Tiger_256 => 'Tiger/256',
            self::RIPEMD_256 => 'RIPEMD-256',
            self::RIPEMD_320 => 'RIPEMD-320',
            self::SHA_384 => 'SHA-384',
            self::SHA3_384 => 'SHA3-384',
            self::SHA_512 => 'SHA-512',
            self::SHA3_512 => 'SHA3-512',
            self::BLAKE2b_512 => 'BLAKE2b-512',
            self::Whirlpool => 'Whirlpool',
            self::Bcrypt => 'Bcrypt',
            self::MD5_crypt => 'MD5 crypt',
            self::SHA_256_crypt => 'SHA-256 crypt',
            self::SHA_512_crypt => 'SHA-512 crypt',
            self::Argon2 => 'Argon2',
            self::PBKDF2 => 'PBKDF2',
            self::Scrypt => 'Scrypt',

            self::HEX_32 => 'HEX-32',
            self::HEX_40 => 'HEX-40',
            self::HEX_48 => 'HEX-48',
            self::HEX_56 => 'HEX-56',
            self::HEX_64 => 'HEX-64',
            self::HEX_80 => 'HEX-80',
            self::HEX_96 => 'HEX-96',
            self::HEX_128 => 'HEX-128',
            self::CRYPT => 'CRYPT',
        };
    }

    /**
     * Check if the case is an individual hash type (power of 2).
     *
     * @return bool
     */
    public function isIndividual(): bool {
        return ($this->value > 0) && (($this->value & ($this->value - 1)) === 0);
    }

    /**
     * Get individual cases included in this case (if it's a group).
     *
     * @return static[]
     */
    public function getIndividualCases(): array {
        if ($this->isIndividual()) {
            return [$this];
        }

        $cases = [];
        foreach (self::cases() as $case) {
            if ($case->isIndividual() && ($this->value & $case->value)) {
                $cases[] = $case;
            }
        }
        return $cases;
    }

    /**
     * @inheritDoc
     */
    public static function tryFromName(string $name, bool $ignoreCase = false): ?static {
        foreach (self::cases() as $case) {
            if ($ignoreCase) {
                if (strcasecmp($case->name, $name) === 0 || strcasecmp($case->text(), $name) === 0) {
                    return $case;
                }
            } else {
                if ($case->name === $name || $case->text() === $name) {
                    return $case;
                }
            }
        }
        return null;
    }
}
