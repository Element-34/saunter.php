<?php

require_once 'SaunterPHP/Framework/SeleniumConnection.php';
require_once 'SaunterPHP/Framework/SuiteIdentifier.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Log.php';

abstract class SaunterPHP_Framework_SaunterTestCase extends PHPUnit_Framework_TestCase {
    static public $log;
    static public $selenium;
    static public $verificationErrors;

    public function setUp() {
        self::$verificationErrors = array();
        self::$log = Log::singleton('file', $GLOBALS['settings']['logname'], $this->getName());
        self::$selenium = SaunterPHP_Framework_SeleniumConnection::getInstance()->selenium;
        self::$selenium->start();
        self::$selenium->windowMaximize();
    }

    // fired after the test run but before teardown
    public function assertPostConditions() {
        $this->assertEmpty(self::$verificationErrors, implode("\n", self::$verificationErrors));
    }
  
    public function tearDown() { }
  
    public function verifyCookiePresent($want) {
        try {
            $this->assertTrue(self::$selenium->isCookiePresent($want),  $want . ' cookie is not present.');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push(self::$verificationErrors, $e->toString());
        }
    }

    public function verifyElementAvailable($element) {
        try {
            $this->assertTrue(self::$selenium->isElementPresent($element), $element . ' element is not present.');
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push(self::$verificationErrors, $e->toString());
        }
        if (self::$selenium->isElementPresent($element)) {
            try {
                $this->assertTrue(self::$selenium->isVisible($element), $element . ' element is not available.');
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                array_push(self::$verificationErrors, $e->toString());
            }
        }
    }
    
    public function verifyEquals($want, $got) {
        try {
            $this->assertEquals($want, $got);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push(self::$verificationErrors, $e->toString());
        }
    }
    
    public function verifyTrue($condition, $message = "") {
        try {
            $this->assertTrue($condition);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            if ($message) {
                array_push(self::$verificationErrors, $message);
            } else {
                array_push(self::$verificationErrors, $e->toString());
            }
        }
    }
}
?>