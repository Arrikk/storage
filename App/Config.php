<?php
namespace App;

/**
 * Config Settings
 * 
 * PHP version 7.4.8
 */

class Config
{
    /**
     * DB Host
     * 
     * @var string
     */
    CONST DB_HOST = 'localhost';
    /**
     * DB name
     * 
     * @var string
     */
    CONST DB_NAME = 'test';
    /**
     * DB username
     * 
     * @var string
     */
    CONST DB_USER = 'root';
    /**
     * DB Password
     * 
     * @var string
     */
    CONST DB_PASSWORD =  '';
    /**
     * Error
     * 
     * @var bool
     */
    CONST SHOW_ERROR = true;
    /**
     * Base Url
     * 
     * @var string
     */
    CONST BASE_URL = '';
    /**
     * Secret Key
     * 
     * @var string
     */
    CONST SECRET_KEY = 'Op9O+=/CO3eE+9+Cs222p/qdEFeneD';
    /**
     * Show flash message
     * 
     * @var bool
     */
    CONST FLASH = true;
    /**
     * Set default email address
     * 
     * @return string
     */
    CONST DEFAULT_EMAIL = '';
}
            
            