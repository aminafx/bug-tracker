<?php
namespace App\Helpers;
use App\Exceptions\ConfigFileNotFoundException;

class Config
{
    public static function getFileContent(string $filename)
    {
        $filePath = realpath(__DIR__. "/../configs/".$filename.'.php');

        if(!$filePath){
            throw new ConfigFileNotFoundException();
        }
        $fileContents = require $filePath;
        return $fileContents;
    }

    /**
     * @throws ConfigFileNotFoundException
     */
    public static function get(string $filename, string $key=null)
    {
        $fileContents = self::getFileContent($filename);
        if(is_null($key)) return $fileContents;

        return $fileContents[$key]?? null;
    }
}