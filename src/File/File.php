<?php

namespace System\File;

class File
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
    public static function Root()
    {
        return ROOT;
    }
    /**
     * 
     */
    public static function DS()
    {
        return DS;
    }
    /**
     * 
     */
    public static function path($path)
    {
        $path = static::Root() . static::DS() . trim($path, '/');
        $path = str_replace(['/', '\\'], static::DS(), $path);
        return $path;
    }
    /**
     * 
     */
    public static function exists($path)
    {
        return file_exists(static::path($path));
    }
    /**
     * 
     */
    public static function getFile($path, $type = 'R')
    {
        if (static::exists($path) && strtoupper($type) === 'R') {
            return require_once static::path($path);
        } else if (static::exists($path) && strtoupper($type) === 'I') {
            return include_once static::path($path);
        }
    }
    /**
     * 
     */
    public static function scanDir($path)
    {
        return array_diff(scandir(static::path($path)), ['.', '..']);
    }
    /**
     * 
     */
    public static function requireDir($dir)
    {
        foreach (static::scanDir($dir) as $file) {
            $path = $dir . static::DS() . $file;
            if (static::exists($path)) {
                static::getFile($path);
            }
        }
    }
}
