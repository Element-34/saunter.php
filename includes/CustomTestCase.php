<?php

require_once 'SeleniumConnection.php';
require_once 'PHPUnit/Framework/TestCase.php';

abstract class CustomTestCase extends PHPUnit_Framework_TestCase {

  public function setUp() {
      $this->verificationErrors = array();
      
      $this->selenium = SeleniumConnection::getInstance()->selenium;
      $this->selenium->start();
      $this->selenium->windowMaximize();
  }

  public function tearDown()
  {
      $this->selenium->stop();
      if (count($this->verificationErrors) != 0)
      {
        $this->fail(implode("\n", $this->verificationErrors));
      }
  }
}
?>