<?php

namespace App\Interfaces;

interface ValueStoreInterface
{
    public const DELIMITER = '.';

    /**
     * @param string|null $key
     * @return mixed
     */
    public function get(?string $key = null);

    /**
     * @param string $key
     * @param mixed $value
     * @return ValueStoreInterface
     */
    public function set(string $key, $value): ValueStoreInterface;

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool;

    /**
     * @param string $key
     * @return bool
     */
    public function remove(string $key): bool;

    /**
     * @param string $path
     * @param string $delimiter
     * @return mixed
     */
    public function getArrayPath(string $path, string $delimiter = self::DELIMITER);

    /**
     * @param string $path
     * @param mixed $value
     * @param string $delimiter
     * @return ValueStoreInterface
     */
    public function setArrayPath(string $path, $value, $delimiter = self::DELIMITER): ValueStoreInterface;
}