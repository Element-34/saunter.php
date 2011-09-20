<?php

require_once 'SaunterPHP/Framework/Bindings/SaunterRemoteControl.php';

class SaunterPHP_Framework_SeleniumConnection {
  // Store the single instance of Selenium server 
  private static $m_pInstance; 

  private function __construct() {
    // this would normally be driven by a config file
    $this->selenium = new SaunterPHP_Framework_Bindings_SaunterRemoteControl($GLOBALS['settings']['browser'],
                                                                             $GLOBALS['settings']['webserver'],
                                                                             $GLOBALS['settings']['seleniumserver'],
                                                                             $GLOBALS['settings']['seleniumport']);
  }

  public static function getInstance() 
  { 
      if (!self::$m_pInstance) 
      { 
          self::$m_pInstance = new SaunterPHP_Framework_SeleniumConnection(); 
      } 

      return self::$m_pInstance; 
  }
}


?>