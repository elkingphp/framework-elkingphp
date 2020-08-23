<?php

namespace System\Router;

use System\Http\Request;
use System\View\Views;

class Route
{
    /**
     * 
     */
    private static $routes = [];
    /**
     * 
     */
    private static $level;
    /**
     * 
     */
    private static $prefix;
    /**
     * 
     */
    private function __construct()
    {
    }
    /**
     * 
     */
    private static function add($methods, $uri, $callBack)
    {
        $uri = rtrim(static::$prefix . "/" . trim($uri, "/"), "/");
        $uri = $uri ?: '/';
        foreach (explode('|', $methods) as $method) {
            static::$routes[] = [
                "uri"       => $uri,
                "callback"  => $callBack,
                "method"    => $method,
                "level"     => static::$level
            ];
        }
    }
    /**
     * 
     */
    public static function get($uri, $callBack)
    {
        static::add("GET", $uri, $callBack);
    }
    /**
     * 
     */
    public static function post($uri, $callBack)
    {
        static::add("POST", $uri, $callBack);
    }
    /**
     * 
     */
    public static function any($uri, $callBack)
    {
        static::add("GET|POST", $uri, $callBack);
    }
    /**
     * 
     */
    public static function prefix($prefix, $callBack)
    {
        $defultPrefix = static::$prefix;
        static::$prefix .= "/" . trim($prefix, "/");
        if (is_callable($callBack)) {
            call_user_func($callBack);
        } else {
            throw new \Exception("Verify that CallBack Prefix is typing correctly");
        }
        static::$prefix = $defultPrefix;
    }
    /**
     * 
     */
    public static function level($level, $callBack)
    {
        $defultLevel = static::$level;
        static::$level .= "|" . trim($level, "|");
        if (is_callable($callBack)) {
            call_user_func($callBack);
        } else {
            throw new \Exception("Verify that CallBack Level is typing correctly");
        }
        static::$level = $defultLevel;
    }
    /**
     * 
     */
    public static function handel()
    {
        $uri = Request::getURL();
        foreach (static::$routes as $route) {
            $matched        = true;
            $route['uri']   = preg_replace('/\/{(.*?)}/', '/(.*?)', $route['uri']);
            $route['uri']   = '#^' . $route['uri'] . '$#';

            if (preg_match($route['uri'], $uri, $result)) {
                array_shift($result);
                $parameters = array_values($result);
                foreach ($parameters as $parameter) {
                    if (strpos($parameter, "/")) {
                        $matched = false;
                    }
                }
                if ($route['method'] !== Request::method()) {
                    $matched = false;
                }
                if ($matched === true) {
                    return static::invock($route, $parameters);
                }
            }
        }
        return Views::viewBluad("errors.404");
    }
    /**
     * 
     */
    public static function invock($route, $parameters = [])
    {
        static::executeLevel($route);
        $callBack = $route['callback'];
        if (is_callable($callBack)) {
            return call_user_func_array($callBack, $parameters);
        } else if (false !== strpos($callBack, "@")) {
            $callBacks  = explode("@", $callBack);
            $controller = $callBacks[0];
            $CMethod    = $callBacks[1];
            $controller = "App\Controllers\\" . $controller;
            if (class_exists($controller)) {
                $obj = new $controller;

                if (method_exists($obj, $CMethod)) {
                    return call_user_func_array([$obj, $CMethod], $parameters);
                } else {
                    throw new \Exception("The Method " . $CMethod . " is not exist at " . $controller . "");
                }
            } else {
                throw new \Exception("Not Found Controllers " . $controller . "");
            }
        } else {
            throw new \Exception("Not Found Callback Function");
        }
    }
    /**
     * 
     */
    public static function executeLevel($route)
    {
        foreach (explode("|", $route['level']) as $level) {
            if ($level !== "") {
                $baseLevel = 'App\Level\\' . $level;
                if (class_exists($baseLevel)) {
                    $obj = new $baseLevel;
                    call_user_func_array([$obj, 'start'], []);
                } else {
                    throw new \Exception("Not Found Level " . $baseLevel . "");
                }
            }
        }
    }
}
