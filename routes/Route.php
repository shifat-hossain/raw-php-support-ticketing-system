<?php

class Route
{
    public static function handle() {
        
    }
    public static function get($uri, $callback)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === $uri) {
            return call_user_func($callback);
        }
    }

    public static function post($uri, $callback)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === $uri) {
            call_user_func($callback);
        }
    }
    public static function put($uri, $callback)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $_SERVER['REQUEST_URI'] === $uri) {
            call_user_func($callback);
        }
    }
    public static function delete($uri, $callback)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $_SERVER['REQUEST_URI'] === $uri) {
            call_user_func($callback);
        }
    }
    

}