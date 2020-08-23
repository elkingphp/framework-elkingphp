<?php

namespace System\View;

use Exception;
use System\File\File;
use Jenssegers\Blade\Blade;
use System\Session\Session;

class Views
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
    public static function viewBluad($path, $data = [])
    {
        $errors = Session::flash('errors');
        $old_data = Session::flash('old_data');
        $data = array_merge($data, array('errors' => $errors, 'old_data' => $old_data));

        $blade = new Blade(File::path('views'), File::path('storage/cache'));

        return $blade->make($path, $data)->render();
    }
    /**
     * 
     */
    public static function viewElkingphp($path, $data = [])
    {
        $errors = Session::flash('errors');
        $old_data = Session::flash('old_data');
        $data = array_merge($data, array('errors' => $errors, 'old_data' => $old_data));

        $path = "views" . File::DS() . str_replace(['\\', '/', '.'], File::DS(), $path) . ".php";
        if (!File::exists($path)) {
            throw new \Exception("Not Found File Paht " . $path . "");
        }
        ob_start();
        extract($data);
        include File::path($path);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}
