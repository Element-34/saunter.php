<?php
/**
 * @package SaunterPHP
 * @subpackage Framework_PO_WebDriver
 */

require_once 'SaunterPHP/Framework/Exception.php';

class SaunterPHP_Framework_PO_WebDriver_Page {
  
    public static $string_timeout;
    public static $driver;

    // constructor
    function __construct() {
        self::$string_timeout = $GLOBALS['timeouts']["str_ms"];
        self::$driver = SaunterPHP_Framework_SeleniumConnection::WebDriver();
    }  

    function __destruct() {
   
    }
    
}

?>