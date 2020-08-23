<?php

namespace System\Http;

class Response
{
    /**
     * 
     */
    private function __construct()
    {
    }
    /**
     * 
     */
    public static function out($data)
    {
        if (!$data) {
            return;
        } else if (is_array($data) || is_object($data)) {
            $data = static::json($data);
        } else if (is_string($data)) {
            $data = $data;
        }
        echo $data;
    }
    /**
     * 
     */
    public static function json($data)
    {
        return json_encode($data);
    }
}
