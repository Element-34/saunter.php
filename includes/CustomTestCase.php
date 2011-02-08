<?php

require_once 'SeleniumConnection.php';
require_once 'SuiteIdentifier.php';
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
        // job name
        $context = array("name" => $this->getName());

        // job result
        if ($this->status == PHPUnit_Runner_BaseTestRunner::STATUS_PASSED) {
          $context["passed"] = True;
        } else {
          $context["passed"] = False;
        }

        // job tags
        $context["tags"] = array();
        $reflector = new ReflectionMethod($this, $this->name);
        preg_match_all("(@group .*)", $reflector->getDocComment(), $raw_tags);
        if (count($raw_tags[0]) > 0) {
          foreach ($raw_tags[0] as $raw_tag) {
            $split_tag = split(" ", $raw_tag);
            array_push($context["tags"], $split_tag[1]);
          }
        }
        
        // suite identifier
        array_push($context["tags"], SuiteIdentifier::getInstance()->suiteId);
        
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