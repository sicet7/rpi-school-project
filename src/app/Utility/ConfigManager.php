<?php

declare(strict_types=1);

namespace App\Utility;

use App\Exceptions\ConfigException;

/**
 * Class ConfigManager
 * @package App\Utility
 */
class ConfigManager
{
    private const DELIMITER = '.';

    /**
     * @var array
     */
    private array $data = [];

    /**
     * @param string $name
     * @param mixed $data
     * @return ConfigManager
     * @throws ConfigException
     */
    public function set(string $name, $data): ConfigManager
    {
        $name = trim($name, static::DELIMITER . " \t\n\r\0\x0B");
        if (stristr($name, static::DELIMITER) !== false) {
            return $this->setPath(
                explode(static::DELIMITER, $name),
                $data
            );
        }
        if ($data instanceof ConfigException && array_key_exists($name, $this->data)) {
            unset($this->data[$name]);
        } else {
            $this->data[$name] = $data;
        }
        return $this;
    }

    /**
     * @param string|null $name if null will return all data otherwise it will look for the key.
     * @return mixed
     */
    public function get(?string $name = null)
    {
        if ($name === null) {
            return $this->data;
        }
        $name = trim($name, static::DELIMITER . " \t\n\r\0\x0B");
        if (stristr($name, static::DELIMITER) !== false) {
            try {
                return $this->getPath(explode(static::DELIMITER, $name));
            } catch (ConfigException $exception) {
                return null;
            }
        }
        return (isset($this->data[$name]) ? $this->data[$name] : null);
    }

    /**
     * @param string|null $name if null it will check if any data exists, otherwise it will look for the key.
     * @return bool
     */
    public function exists(?string $name = null): bool
    {
        if ($name === null) {
            return !empty($this->data);
        }
        $name = trim($name, static::DELIMITER . " \t\n\r\0\x0B");
        if (stristr($name, static::DELIMITER) !== false) {
            try {
                $this->getPath(explode(static::DELIMITER, $name));
                return true;
            } catch (ConfigException $exception) {
                return false;
            }
        }
        return array_key_exists($name, $this->data);
    }

    /**
     * @param string $name
     * @return bool returns true if it removed something, false otherwise
     */
    public function remove(string $name): bool
    {
        if (!$this->exists($name)) {
            return false;
        }
        try {
            $this->set($name, new ConfigException());
        } catch (ConfigException $exception) {
            // we dont care if it fails to find the key, that just means the value isn't set
        }
        return true;
    }

    /**
     * @param string[] $path
     * @param array|null $data
     * @param string|null $dataKey
     * @return mixed|null
     * @throws ConfigException
     */
    private function getPath(array $path, ?array &$data = null, ?string $dataKey = null)
    {
        if ($dataKey === null) {
            $dataKey = array_pop($path);
        }
        if ($data === null) {
            $data = $this->data;
        }
        if (empty($path) && array_key_exists($dataKey, $data)) {
            return $data[$dataKey];
        }
        if (empty($path) && !array_key_exists($dataKey, $data)) {
            throw new ConfigException('Key not found');
        }
        $currentKey = array_shift($path);
        if (!array_key_exists($currentKey, $data) || !is_array($data[$currentKey])) {
            throw new ConfigException('Key not found');
        }
        return $this->getPath($path, $data[$currentKey], $dataKey);
    }

    /**
     * @param string[] $path
     * @param mixed|ConfigException $value if ConfigException is passed as the value, it will try to unset the path.
     * @param array|null $data
     * @param string|null $dataKey
     * @return ConfigManager
     * @throws ConfigException
     */
    private function setPath(array $path, $value, ?array &$data = null, ?string $dataKey = null): ConfigManager
    {
        if ($dataKey === null) {
            $dataKey = array_pop($path);
        }
        if ($data === null) {
            $data = $this->data;
        }
        if (empty($path) && is_array($data)) {
            if ($value instanceof ConfigException && array_key_exists($dataKey, $data)) {
                unset($data[$dataKey]);
            } else {
                $data[$dataKey] = $value;
            }
        }
        $currentKey = array_shift($path);
        if (array_key_exists($currentKey, $data) && !is_array($data[$currentKey])) {
            throw new ConfigException(
                'Cannot set value because a non-array value is saved in the given path.'
            );
        }
        $this->setPath($path, $value, $data[$currentKey], $dataKey);
        return $this;
    }
}
