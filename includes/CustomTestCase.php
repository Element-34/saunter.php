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

  public function tearDown()
  {
    if ($GLOBALS['settings']['sauce.ondemand'] == "true")
    {
        $context = array("name" => $this->getName(),
                         "passed" => True);
        $jobContext = json_encode($context);
        $this->selenium->setContext("sauce: job-info=$jobName");
    }

    if (count($this->verificationErrors) != 0)
    {
      if ($GLOBALS['settings']['sauce.ondemand'] == "true")
      {
          $context = array("passed" => False);
          $jobContext = json_encode($context);
          $this->selenium->setContext("sauce: job-info=$jobName");
      }
      $this->selenium->stop();
      $this->fail(implode("\n", $this->verificationErrors));
    }

    $this->selenium->stop();
  }
}
?>