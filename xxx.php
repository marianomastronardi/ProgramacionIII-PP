<?php

require_once './archivo.php';

class XXX{

    function __construct()
    {
        
    }

    function __get($name)
    {
        return $this->$name;
    }

    function __set($name, $value)
    {
        $this->$name = $value;
    }
}

?>