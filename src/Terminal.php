<?php

class Terminal
{
    public static function info($message)
    {
        if (!OS::isWindows()) echo "\033[1;34m";
        echo $message;
        if (!OS::isWindows()) echo "\033[m";
    }
    
    public static function warning($message)
    {
        if (!OS::isWindows()) echo "\033[1;33m";
        echo $message;
        if (!OS::isWindows()) echo "\033[m";
    }

    public static function success($message)
    {
        if (!OS::isWindows()) echo "\033[1;32m";
        echo $message;
        if (!OS::isWindows()) echo "\033[m";
    }
    
    public static function error($message)
    {
        if (!OS::isWindows()) echo "\033[1;31m";
        echo $message;
        if (!OS::isWindows()) echo "\033[m";
    }
    
    public static function bold($message)
    {
        if (!OS::isWindows()) echo "\033[1m";
        echo $message;
        if (!OS::isWindows()) echo "\033[m";
    }

    public static function write($message)
    {
        echo $message;
    }
}
