<?php
/**
 * @package SaunterPHP
 */
 
require_once 'SaunterPHP/Framework/Bindings/SaunterRemoteControl.php';
require_once 'SaunterPHP/Framework/Bindings/SaunterWebDriver.php';

class SaunterPHP_Framework_SeleniumConnection {
    // Store the single instance of Selenium server 
    private static $instance;

    private function __construct() {
    }

    public static function RemoteControl() {
        if (!isset(self::$instance)) {
            self::$instance = new SaunterPHP_Framework_Bindings_SaunterRemoteControl($GLOBALS['settings']['browser'],
                                                                                     $GLOBALS['settings']['webserver'],
                                                                                     $GLOBALS['settings']['seleniumserver'],
                                                                                     $GLOBALS['settings']['seleniumport']);
        }
        return self::$instance;
    }
}


?>