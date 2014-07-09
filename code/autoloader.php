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
    
     public function model($className)
    {
    /* makes this work in spl-esq ways. 
        $className = str_replace('pivot\model\\','',$className);
        $fileName = "../model/". substr($className, 0, strpos($className,'Model')) .".model.php";
        include_once($fileName);
   */      
        set_include_path(get_include_path().PATH_SEPARATOR.'/model/');
        spl_autoload_extensions('.model.php');
        spl_autoload($class);
    }

}