<?php

class Cypher
{

    /**
     * Hashes a password using the default hashing algorithm
     *
     * @param string $password The password to be hashed
     * @return string The hashed password
     */
    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verifies a password against a hash
     *
     * @param string $password The password to be verified
     * @param string $hash The hash to be verified against
     * @return boolean True if the password matches the hash, false otherwise
     */
    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
