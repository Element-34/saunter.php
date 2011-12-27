<?php
/**
 * @package SaunterPHP
 * @subpackage Framework_TestCase
 */
namespace WebDriver;
 
require_once 'SaunterPHP/Framework/SeleniumConnection.php';
require_once 'SaunterPHP/Framework/SuiteIdentifier.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Log.php';

abstract class SaunterPHP_Framework_SaunterTestCase extends \PHPUnit_Framework_TestCase {
    static public $log;
    static public $verificationErrors;

    public function setUp() {
        self::$verificationErrors = array();
        self::$log = \Log::singleton('file', $GLOBALS['settings']['logname'], $this->getName());
        
        if ($GLOBALS['settings']['sauce.ondemand']) {
            $command_executor = "http://" . $GLOBALS['saucelabs']['username'] . ":" . $GLOBALS['saucelabs']['key'] . "@ondemand.saucelabs.com:80/wd/hub";
        } else {
            $command_executor = "http://" . $GLOBALS['settings']['seleniumserver'] . ":" . $GLOBALS['settings']['seleniumport'] . "/wd/hub";          
        }
        $this->driver = new \SaunterPHP_Framework_Bindings_SaunterWebDriver($command_executor);

        // this is inefficient, but...
        $decoded = json_decode($GLOBALS['settings']['browser'], true);

        // since the config can be shared between, take out the rc *
        if (substr($decoded["browser"], 0, 1) === "*") {
            $browser = substr($decoded["browser"], 1);
        } else {
            $browser = $decoded["browser"];
        }

        if ($GLOBALS['settings']['sauce.ondemand']) {
            $additional_capabilities = array();
            switch ($decoded["os"]) {
                case 'Windows 2003':
                    $additional_capabilities["platform"] = "XP";
                    break;
                case 'Windows 2008':
                    $additional_capabilities["platform"] = "VISTA";
                    break;
                default:
                    $additional_capabilities["platform"] = "LINUX";
            }
            if (substr($decoded["browser-version"], -1, 1) === ".") {
                $additional_capabilities["version"] = substr($decoded["browser-version"], 0, -1);
            } else {
                $additional_capabilities["version"] = $decoded["browser-version"];
            }
        } else {
            $additional_capabilities = array();          
        }
        $this->session = $this->driver->session($browser, $additional_capabilities);
        
        $this->sessionId = substr($this->driver->getURL(), strrpos($this->driver->getURL(), "/") + 1);
    }

    // fired after the test run but before teardown
    public function assertPostConditions() {
        $this->assertEmpty(self::$verificationErrors, implode("\n", self::$verificationErrors));
    }
  
    public function tearDown() { }
    
    // /**
    //  * Verifies that the requested cookie has been set
    //  *
    //  * @param string $want
    //  * @access public
    //  * @return void
    //  */
    // public function verifyCookiePresent($want) {
    //     try {
    //         $this->assertTrue(self::$selenium->isCookiePresent($want),  $want . ' cookie is not present.');
    //     } catch (PHPUnit_Framework_AssertionFailedError $e) {
    //         array_push(self::$verificationErrors, $e->toString());
    //     }
    // }
    // 
    // public function verifyCookieNotPresent($want)
    // {
    //   try {
    //       $this->assertTrue(!self::$selenium->isCookiePresent($want), $want . ' cookie is present.');
    //   } catch (PHPUnit_Framework_AssertionFailedError $e) {
    //       array_push(self::$verificationErrors, $e->toString());
    //   }
    // }
    // 
    // public function verifyElementAvailable($element) {
    //     try {
    //         $this->assertTrue(self::$selenium->isElementPresent($element), $element . ' element is not present.');
    //     } catch (PHPUnit_Framework_AssertionFailedError $e) {
    //         array_push(self::$verificationErrors, $e->toString());
    //     }
    //     if (self::$selenium->isElementPresent($element)) {
    //         try {
    //             $this->assertTrue(self::$selenium->isVisible($element), $element . ' element is not available.');
    //         } catch (PHPUnit_Framework_AssertionFailedError $e) {
    //             array_push(self::$verificationErrors, $e->toString());
    //         }
    //     }
    // }
    // 
    // public function verifyElementNotPresent($element) {
    //     try {
    //         $this->assertTrue(!self::$selenium->isElementPresent($element), $element . ' element is present.');
    //     } catch (PHPUnit_Framework_AssertionFailedError $e) {
    //         array_push(self::$verificationErrors, $e->toString());
    //     }
    // }
    // 
    public function verifyEquals($want, $got, $message = "") {
        try {
            $this->assertEquals($want, $got);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
          if ($message) {
              array_push(self::$verificationErrors, $message);
          } else {
              array_push(self::$verificationErrors, $e->toString());
          }
        }
    }
    
    // public function verifyLocation($relativeURL) {
    //     try {
    //         $this->assertEquals($GLOBALS['settings']['webserver'] . $relativeURL, self::$selenium->getLocation(),  "URLs don't match with, " . $relativeURL);
    //     } catch (PHPUnit_Framework_AssertionFailedError $e) {
    //         array_push(self::$verificationErrors, $e->toString());
    //     }
    // }
    // 
    // public function verifyNotEquals($want, $got) {
    //     try {
    //         $this->assertNotEquals($want, $got);
    //     } catch (PHPUnit_Framework_AssertionFailedError $e) {
    //         array_push(self::$verificationErrors, $e->toString());
    //     }
    // }
    // 
    // public function verifyNotLocation($relativeURL)
    // {
    //   try {
    //       $this->assertNotEquals($GLOBALS['settings']['webserver'] . $relativeURL, self::$selenium->getLocation(), "URLs still match with, " . $relativeURL);
    //   } catch (PHPUnit_Framework_AssertionFailedError $e) {
    //       array_push(self::$verificationErrors, $e->toString());
    //   }
    // }
    // 
    // public function verifyNotTextRegEx($element,$pattern) {
    //     try {
    //         $this->assertTrue(!(bool)preg_match($pattern,self::$selenium->getText($element)));
    //     } catch (PHPUnit_Framework_AssertionFailedError $e) {
    //         array_push(self::$verificationErrors, $e->toString());
    //     }
    // }
    // 
    // public function verifyTextPresent($want) {
    //     try {
    //         $this->assertTrue(self::$selenium->isTextPresent($want), $want . ' Text is not present.');
    //     } catch (PHPUnit_Framework_AssertionFailedError $e) {
    //         array_push(self::$verificationErrors, $e->toString());
    //     }
    // }
    // 
    // public function verifyTextNotPresent($want) {
    //     try {
    //         $this->assertTrue(!self::$selenium->isTextPresent($want), $want . ' Text is present.');
    //     } catch (PHPUnit_Framework_AssertionFailedError $e) {
    //         array_push(self::$verificationErrors, $e->toString());
    //     }
    // }
    // 
    // public function verifyTextRegEx($element,$pattern) {
    //     try {
    //         $this->assertTrue((bool)preg_match($pattern,self::$selenium->getText($element)));
    //     } catch (PHPUnit_Framework_AssertionFailedError $e) {
    //         array_push(self::$verificationErrors, $e->toString());
    //     }
    // }
    // 
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

    public function verifyFalse($condition, $message = "") {
        try {
            $this->assertFalse($condition);
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