<?php

namespace Core;

use Core\Http\Res;

class Request
{
    public $request;
    function __construct($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    public static function get()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $request = new Request($_GET);
        return $request;
    }
    public static function post()
    {
        header('Access-Control-Allow-Origin: *');
        // header('Access-Control-Allow-Headers: Content-Type');

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $data = $_POST;
        if(empty($data)) throw new \Exception("POST not Found");
        $request = new Request($data);
        return $request;
    }
}