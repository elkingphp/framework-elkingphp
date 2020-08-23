<?php

namespace System\Url;

use System\Http\Request;
use System\Http\Server;

class Url
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
    public static function path($path)
    {
        return Request::BaseURL() . "/" . trim($path, "/");
    }
    /**
     * 
     */
    public static function ref_path()
    {
        return Server::get('HTTP_REFERER');
    }
    /**
     * 
     */
    public static function redir($path)
    {
        header("Location:" . $path . "");
        exit();
    }
}
