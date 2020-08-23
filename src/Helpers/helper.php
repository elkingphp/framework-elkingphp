<?php

/**
 * 
 */
if (!function_exists("view")) {
    function view($path, $data = [])
    {
        return \System\View\Views::viewElkingphp($path, $data);
        //return \System\View\Views::viewBluad($path, $data);
    }
}
/**
 * 
 */
if (!function_exists("req")) {
    function req($key)
    {
        return \System\Http\Request::value($key);
    }
}
/**
 * 
 */
if (!function_exists("reDir")) {
    function reDir($path)
    {
        return \System\Url\Url::redir($path);
    }
}
/**
 * 
 */
if (!function_exists("url")) {
    function url($path)
    {
        return \System\Url\Url::path($path);
    }
}
