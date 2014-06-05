<?php

/*
 * Autloader
 * Shamelessly stolen from php comment by "fka at fatihkadirakin dot com"
 * 
 */ 

class autoloader
{
    public static $loader;
    
    
    public static function init()
    {
        if (self::$loader == NULL)
            self::$loader = new self();

        return self::$loader;
    }

    public function __construct()
    {
        spl_autoload_register(array($this,'model'));
        #spl_autoload_register(array($this,'helper'));
        #spl_autoload_register(array($this,'controller'));
        #spl_autoload_register(array($this,'library'));
    }
    
     public function model($class)
    {
        $class = preg_replace('/pivot\/model$/ui','',$class);
        
        set_include_path(get_include_path().PATH_SEPARATOR.'/model/');
        spl_autoload_extensions('.model.php');
        spl_autoload($class);
    }

}