<?php

require_once 'SeleniumConnection.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Log.php';

abstract class CustomTestCase extends PHPUnit_Framework_TestCase {
  
  public function setUp() {
    $this->verificationErrors = array();
    $this->log = Log::singleton('file', $GLOBALS['settings']['logname'], $this->name);
    $this->selenium = SeleniumConnection::getInstance()->selenium;
    $this->selenium->start();
    $this->selenium->windowMaximize();
  }

  // fired after the test run but before teardown
  public function assertPostConditions() {
    $this->assertEmpty($this->verificationErrors, implode("\n", $this->verificationErrors));
  }
  
  public function tearDown()
  {
    if ($GLOBALS['settings']['sauce.ondemand'] == "true")
    {   
        $context = array("name" => $this->getName());
        if ($this->status == PHPUnit_Runner_BaseTestRunner::STATUS_PASSED) {
          $context["passed"] = True;
        } else {
          $context["passed"] = False;
        }

        $jsonContext = json_encode($context);
        $this->selenium->setContext("sauce: job-info=$jsonContext");
    }
    $this->selenium->stop();
  }
  
  public function verifyEquals($want, $got)
  {
    try {
        $this->assertEquals($want, $got);
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
  }
}
?>