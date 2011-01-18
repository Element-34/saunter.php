<?php
class BasePage {
  
  public static $string_timeout = "30000"; // 30 seconds
  
  // constructor
  function __construct() {
       $this->selenium = SeleniumConnection::getInstance()->selenium;
   }  

   public function waitForElementAvailable($element)
   {
     for ($second = 0; ; $second++) {
         if ($second >= 60) $this->fail("timeout for element " . $element . " present");
         try {
             if ($this->selenium->isElementPresent($element)) break;
         } catch (Exception $e) {}
         sleep(1);
     }
     for ($second; ; $second++) {
         if ($second >= 60) $this->fail("timeout for element " . $element . " visibility");
         try {
             if ($this->selenium->isVisible($element)) break;
         } catch (Exception $e) {}
         sleep(1);
     }
   }
}
?>