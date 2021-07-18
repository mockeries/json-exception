<?php

namespace Mockeries\JsonException;

class JsonException
{
    protected $data = [];
    private $exceptions = ['2710', 'E32'];

    /**
     * Constructor
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function jsonException($keys = [])
    {
        $int_exception = false;
        $str_exception = false;

        foreach ($keys as $key) {
            if (is_string($this->data[$key])) {
                if (bin2hex($this->data[$key]) == $this->exceptions[1]) {
                    $str_exception = true;
                }
            } elseif (is_int($this->data[$key])) {
                if (dechex($this->data[$key]) == $this->exceptions[0]) {
                    $int_exception = true;
                }
            }
        }

        return !$int_exception && !$str_exception ? $this->data : false;
    }
}