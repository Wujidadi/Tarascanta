<?php

namespace App\Classes;

class Demo
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
     * Get welcome message.
     *
     * @param  string  $message  String to form the welcom message
     * @return string
     */
    public function welcome($message)
    {
        echo 'Welcome to ' . $message;
    }
}
