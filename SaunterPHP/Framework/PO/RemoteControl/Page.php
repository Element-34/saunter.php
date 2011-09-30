<?php

require_once 'SaunterPHP/Framework/Exception.php';

class SaunterPHP_Framework_PO_RemoteControl_Page {
  
  public static $string_timeout = "30000"; // 30 seconds
  public static $selenium;
  
  // constructor
  function __construct() {
      self::$selenium = SaunterPHP_Framework_SeleniumConnection::getInstance()->selenium;
   }  

   function __destruct() {
       
   }

   public function waitForElementAvailable($element)
   {
     for ($second = 0; ; $second++) {
         if ($second >= 60) {
             throw new Saunter_Framework_Exception("timeout for element " . $element . " present");
         }
         try {
             if (self::$selenium->isElementPresent($element)) break;
         } catch (Exception $e) {}
         sleep(1);
     }
     for ($second; ; $second++) {
         if ($second >= 60) {
            throw new Saunter_Framework_Exception("timeout for element " . $element . " visibility");
         }
         try {
             if (self::$selenium->isVisible($element)) break;
         } catch (Exception $e) {}
         sleep(1);
     }
   }
}
?>