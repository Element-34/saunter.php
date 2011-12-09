<?php
/**
 * @package SaunterPHP
 * @subpackage Framework_PO_WebDriver
 */

require_once 'SaunterPHP/Framework/Exception.php';
include_once 'PHPWebDriver/WebDriverWait.php';

class SaunterPHP_Framework_PO_WebDriver_Page {
  
    public static $string_timeout;
    public static $session;

    // constructor
    function __construct($session) {
        self::$string_timeout = $GLOBALS['timeouts']["str_ms"];
        self::$session = $session;
    }  

    function __destruct() {
   
    }
    
    function wait_for_value_changed($where, $what) {
        $element = self::$session->find_element_by_locator($where);
        foreach (range(0, $GLOBALS['timeouts']["seconds"]) as $value) {
            try {
                $text = $element->text();
                if ((strlen(trim($text)) != 0) && trim($text) != $what) {
                    return True;
                }
            } catch (PHPWebDriver_ObsoleteElementWebDriverError $e) {
                $element = self::$session->find_element_by_locator($where);
            }
            sleep(1);
        }
        throw new SaunterPHP_Framework_TimeoutError(
          sprintf(
            'Element with locator "%s" timed out waiting for it not to be "%s"',
            $where,
            $what));
    }
}

?>