<?php
/**
 * @package SaunterPHP
 * @subpackage Framework_PO_WebDriver
 */

require_once 'SaunterPHP/Framework/Exception.php';
include_once 'PHPWebDriver/WebDriverWait.php';

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
    
    function wait_for_value_changed($where, $what) {
        $element = self::$driver->find_element_by_locator($where);
        foreach (range(0, $GLOBALS['timeouts']["seconds"]) as $value) {
            try {
                $text = $element->text();
                if ((strlen(trim($text)) != 0) && trim($text) != $what) {
                    return True;
                }
            } catch (PHPWebDriver_ObsoleteElementWebDriverError $e) {
                $element = self::$driver->find_element_by_locator($where);
            }
            sleep(1);
        }
        return False;
    }
}

?>