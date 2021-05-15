<?php

namespace App\Controllers;

use App\Classes\Demo;

class DemoController
{
    /**
     * Instance of this class.
     *
     * @var self|null
     */
    protected static $_instance = null;

    /**
     * Get the instance of this class.
     * 
     * @return self
     */
    public static function getInstance()
    {
        if (self::$_instance == null)
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Main demo method.
     *
     * @return void
     */
    public function main()
    {
        echo Demo::getInstance()->welcome('Tarascanta Welcome Page');
    }

    /**
     * Main demo API method.
     *
     * @return void
     */
    public function api()
    {
        header('Content-Type: text/plain');

        echo 'API';
    }
}
