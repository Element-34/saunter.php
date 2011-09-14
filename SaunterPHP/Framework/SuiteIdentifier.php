<?php

class SaunterPHP_Framework_SuiteIdentifier {
  private static $m_pInstance; 

  private function __construct() {
    $this->suiteId = uniqid('run-');
  }

  public static function getInstance() 
  { 
      if (!self::$m_pInstance) 
      { 
          self::$m_pInstance = new SuiteIdentifier(); 
      } 
      return self::$m_pInstance; 
  }
}
?>