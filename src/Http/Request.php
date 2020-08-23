<?php

namespace System\Http;

class Request
{
    /**
     * 
     */
    private static $scriptName;
    /**
     * 
     */
    private static $BaseURL;
    /**
     * 
     */
    private static $URL;
    /**
     * 
     */
    private static $fullURL;
    /**
     * 
     */
    private static $queryString;
    /**
     * 
     */
    private function __construct()
    {
    }
    /**
     * 
     */
    public static function handle()
    {
        static::$scriptName = str_replace("\\", "", dirname(Server::get('SCRIPT_NAME')));
        static::setBaseUrl();
        static::setUrl();
    }
    /**
     * 
     */
    private static function setBaseUrl()
    {
        $protocol               = Server::get('REQUEST_SCHEME') . '://';
        $hostname               = Server::get('HTTP_HOST');
        $scriptName             = static::$scriptName;
        static::$BaseURL = $protocol . $hostname . $scriptName;
    }
    /**
     * 
     */
    private static function setUrl()
    {
        $Request_URI = urldecode(Server::get('REQUEST_URI'));
        $Request_URI = rtrim(preg_replace('#^' . static::$scriptName . '#', '', $Request_URI), '/');

        static::$fullURL = $Request_URI;
        if (false !== strpos($Request_URI, '?')) {
            $cutRequert = explode("?", $Request_URI);
        }
        static::$URL = $cutRequert[0] ?: $Request_URI ?: '/';
        static::$queryString = $cutRequert[1];
    }
    /**
     * 
     */
    public static function BaseURL()
    {
        return static::$BaseURL;
    }
    /**
     * 
     */
    public static function getURL()
    {
        return static::$URL;
    }
    /**
     * 
     */
    public static function getQueryString()
    {
        return static::$queryString;
    }
    /**
     * 
     */
    public static function getFullURL()
    {
        return static::$fullURL;
    }
    /**
     * 
     */
    public static function method()
    {
        return Server::get('REQUEST_METHOD');
    }
    /**
     * 
     */
    public static function _GET($key)
    {
        return static::value($key, $_GET);
    }
    /**
     * 
     */
    public static function _POST($key)
    {
        return static::value($key, $_POST);
    }
    /**
     * 
     */
    public static function check($key, array $type)
    {
        return array_key_exists($key, $type);
    }
    /**
     * 
     */
    public static function value($key, array $type = null)
    {
        $type = isset($type) ? $type : $_REQUEST;
        return static::check($key, $type) ? $type[$key] : null;
    }
    /**
     * 
     */
    public static function set($key, $value)
    {
        $_REQUEST[$key] = $value;
        $_GET[$key]     = $value;
        $_POST[$key]    = $value;
        return $value;
    }
    /**
     * 
     */
    public static function referer()
    {
        return Server::get("HTTP_REFERER");
    }
    /**
     * 
     */
    public static function All()
    {
        return $_REQUEST;
    }
}
