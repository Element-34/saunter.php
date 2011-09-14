<?php

require_once 'Testing/Selenium.php';

class SaunterPHP_Framework_SeleniumConnection {
  // Store the single instance of Selenium server 
  private static $m_pInstance; 

  private function __construct() {
    // this would normally be driven by a config file
    $this->selenium = new Testing_Selenium($GLOBALS['settings']['browser'],
                                           $GLOBALS['settings']['webserver'],
                                           $GLOBALS['settings']['seleniumserver'],
                                           $GLOBALS['settings']['seleniumport']);
  }

  public static function getInstance() 
  { 
      if (!self::$m_pInstance) 
      { 
          self::$m_pInstance = new SeleniumConnection(); 
      } 

      return self::$m_pInstance; 
  }
}


?>