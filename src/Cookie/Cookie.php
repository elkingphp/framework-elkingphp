<?php

namespace System\Cookie;

class Cookie
{
    private function __construct()
    {
    }
    /**
     * 
     */
    public static function set($key, $value)
    {
        $expierd = (time() + 60 * 60 * 24);
        setcookie($key, $value, $expierd, '/', "", false, true);
        return $value;
    }
    /**
     * 
     */
    public static function check($key)
    {
        return isset($_COOKIE[$key]);
    }
    /**
     * 
     */
    public static function get($key)
    {
        return static::check($key) ? $_COOKIE[$key] : null;
    }
    /**
     * 
     */
    public static function delete($key)
    {
        unset($_COOKIE[$key]);
        setcookie($key, null, -1, '/');
    }
    /**
     * 
     */
    public static function getAll()
    {
        return $_COOKIE;
    }
    /**
     * 
     */
    public static function kill()
    {
        foreach (static::getAll() as $key => $value) {
            static::delete($key);
        }
    }
}
