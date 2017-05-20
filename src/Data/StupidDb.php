<?php

namespace roydejong\dotnet\Data;

/**
 * Stupid, static, flat-file, global key/value storage.
 */
class StupidDb
{
    /**
     * @var \stdClass|null
     */
    protected static $values = null;

    /**
     * @var bool
     */
    protected static $open = false;

    /**
     * Gets the path to the StupidDB file.
     *
     * @return string
     */
    protected static function getPath(): string
    {
        return PATH_COMPILATION . "/stupid.db";
    }

    /**
     * Opens this stupid fucking database for reading, creating it if needed.
     */
    protected static function open(): bool
    {
        if (self::$open) {
            return false;
        }

        self::$values = null;

        $dbPath = self::getPath();

        if (!file_exists($dbPath)) {
            file_put_contents($dbPath, "{}");
        }

        self::$values = json_decode(file_get_contents($dbPath));

        if (!self::$values instanceof \stdClass) {
            self::$values = new \stdClass();
        }

        self::$open = true;
        return true;
    }

    /**
     * Commits any changes to database.
     *
     * @return bool
     */
    public static function commit(): bool
    {
        if (!self::$open) {
            return false;
        }

        file_put_contents(self::getPath(), json_encode(self::$values), LOCK_EX);
        return true;
    }

    /**
     * Adds or updates a string value in the database.
     *
     * @param string $key
     * @param string $value
     */
    public static function setString(string $key, string $value): void
    {
        self::open();
        self::$values->$key = $value;
    }

    /**
     * Gets a value by its key.
     *
     * @param string $key
     * @return null|mixed
     */
    public static function getString(string $key): ?string
    {
        self::open();

        if (isset(self::$values->$key)) {
            return strval(self::$values->$key);
        }

        return null;
    }
}