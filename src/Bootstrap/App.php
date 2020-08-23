<?php

namespace System\Bootstrap;

use System\Exceptions\Whoops;
use System\File\File;
use System\Http\Request;
use System\Http\Response;
use System\Router\Route;
use System\Session\Session;

class App
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
    public static function run()
    {
        /**
         * 
         */
        Whoops::handle();
        /**
         * 
         */
        Session::Start();
        /**
         * 
         */
        Request::handle();
        /**
         * 
         */
        File::requireDir("routes");
        /**
         * 
         */
        Route::handel();
        /**
         * 
         */
        Response::out(Route::handel());
        /**
         * 
         */
    }
}
