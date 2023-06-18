<?php
/**
 * Validator class for hashing and verifying passwords using Argon2Id.
 *
 * @package   CanaryAAC
 * @author    Daniel Henrique <daniel15042015@gmail.com>
 * @copyright 2023 CanaryAAC
 */

namespace App\Utils;

use App\Model\Entity\Account as Argondb;

class Argon
{
    /**
     * Hashes a password using the Argon2Id algorithm.
     *
     * @param string $password The plaintext password to hash.
     *
     * @return string The hashed password.
     */
    public static function generateArgonPassword(string $password): string
    {
        $t_cost = 2;
        $m_cost = 1 << 16;
        $parallelism = 2;

        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => $m_cost,
            'time_cost' => $t_cost,
            'threads' => $parallelism,
        ]);
    }

    /**
     * Hashes a password using Argon2Id and updates it for the specified account.
     *
     * @param int $account_id The ID of the account to update.
     * @param string $password The plaintext password to hash and update.
     */
    public static function updateAccountPassword(int $account_id, string $password): void
    {
        $hashed_password = self::generateArgonPassword($password);
        Argondb::updateAccount(['id' => $account_id], [
            'password' => $hashed_password,
        ]);
    }

    /**
     * Compares a plaintext password with an Argon2Id hashed password.
     *
     * @param string $password The plaintext password to compare.
     * @param string $hashed_password The Argon2Id hashed password to compare against.
     *
     * @return bool True if the password matches the hash, false otherwise.
     */
    public static function compareArgonPassword(string $password, string $hashed_password): bool
    {
        return password_verify($password, $hashed_password);
    }


    public static function beats(string $password, string $hashed_password, int $account_id = -1): bool
    {
        if (!self::compareArgonPassword($password, $hashed_password)) {
            if(!self::compareSha1Password($password, $hashed_password)) {
                return false;
            } else {
                if ($account_id != -1) {
                    self::updateAccountPassword($account_id, $password);
                    return true;
                }
            }
        }
        return true;
    }


    /**
     * Compares a plaintext password with an SHA-1 hashed password.
     *
     * @param string $password The plaintext password to compare.
     * @param string $sha1_password The SHA-1 hashed password to compare against.
     *
     * @return bool True if the password matches the hash, false otherwise.
     */
    public static function compareSha1Password(string $password, string $sha1_password): bool
    {
        return sha1($password) === $sha1_password;
    }
}
