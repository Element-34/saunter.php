<?php
/**
 * @package SaunterPHP
 * @subpackage Framework_PO_RemoteControl
 */

require_once 'SaunterPHP/Framework/Exception.php';

class SaunterPHP_Framework_PO_RemoteControl_Page {
  
    public static $string_timeout;
    public static $selenium;

    // constructor
    function __construct() {
        self::$string_timeout = $GLOBALS['timeouts']["str_ms"];
        self::$selenium = SaunterPHP_Framework_SeleniumConnection::getInstance()->selenium;
    }  

    function __destruct() {
   
    }

    public function waitForElementAvailable($element)
    {
     for ($second = 0; ; $second++) {
         if ($second >= $GLOBALS['timeouts']["seconds"]) {
             throw new SaunterPHP_Framework_Exception("timeout for element " . $element . " present");
         }
         try {
             if (self::$selenium->isElementPresent($element)) break;
         } catch (Exception $e) {}
         sleep(1);
     }
     for ($second; ; $second++) {
         if ($second >= $GLOBALS['timeouts']["seconnds"]) {
             throw new SaunterPHP_Framework_Exception("timeout for element " . $element . " visibility");
         }
         try {
             if (self::$selenium->isVisible($element)) break;
         } catch (Exception $e) {}
         sleep(1);
     }
    }
}
?>