<?php

namespace System\Session;

class Session
{
    private function __construct()
    {
    }
    /**
     * 
     */
    public static function Start()
    {
        if (!session_id()) {
            ini_set('session.use_only_cookes', 1);
            session_start();
        }
    }
    /**
     * 
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
        return $value;
    }
    /**
     * 
     */
    public static function check($key)
    {
        return isset($_SESSION[$key]);
    }
    /**
     * 
     */
    public static function get($key)
    {
        return static::check($key) ? $_SESSION[$key] : null;
    }
    /**
     * 
     */
    public static function delete($key)
    {
        unset($_SESSION[$key]);
    }
    /**
     * 
     */
    public static function getAll()
    {
        return $_SESSION;
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
    /**
     * 
     */
    public static function flash($key)
    {
        $value = null;
        if (static::check($key)) {
            $value = static::get($key);
            static::delete($key);
        }
        return $value;
    }
}
