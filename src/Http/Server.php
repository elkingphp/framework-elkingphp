<?php

namespace System\Http;

class Server
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
    public static function check($key)
    {
        return isset($_SERVER[$key]);
    }
    /**
     * 
     */
    public static function get($key)
    {
        return static::check($key) ? $_SERVER[$key] : null;
    }
    /**
     * 
     */
    public static function path_info($path)
    {
        return pathinfo($path);
    }

    public static function all()
    {
        return $_SERVER;
    }
}
