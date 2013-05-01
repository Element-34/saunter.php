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
require_once 'PHPWebDriver/WebDriverProxy.php';
require_once 'PHPBrowserMobProxy/Client.php';

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
        // var_dump($this->driver);

        // this is inefficient, but...
        $decoded = json_decode($GLOBALS['settings']['browser'], true);
        if ($decoded) {
            $browser = $decoded["browser"];
        } else {
            $browser = $GLOBALS['settings']['browser'];
        }

        // since the config can be shared between, take out the rc *
        if (substr($browser, 0, 1) === "*") {
            $browser = substr($browser, 1);
        } 

        $additional_capabilities = array();
        if ($GLOBALS['settings']['sauce.ondemand']) {
            switch ($decoded["os"]) {
                case 'Windows 2003':
                case 'XP':
                    $additional_capabilities["platform"] = "XP";
                    break;
                case 'Windows 2008':
                case 'VISTA':
                    $additional_capabilities["platform"] = "VISTA";
                    break;
                case 'OSX':
                case 'MAC':
                    $additional_capabilities["platform"] = "MAC";
                    break;
                default:
                    $additional_capabilities["platform"] = "LINUX";
            }
            if (substr($decoded["browser-version"], -1, 1) === ".") {
                $additional_capabilities["version"] = substr($decoded["browser-version"], 0, -1);
            } else {
                $additional_capabilities["version"] = $decoded["browser-version"];
            }
            // selenium version
            if (array_key_exists('selenium-version', $GLOBALS['saucelabs'])) {
                $additional_capabilities["selenium-version"] = $GLOBALS['saucelabs']['selenium-version'];
            }
        } else {
            if (array_key_exists('proxy', $GLOBALS['settings']) && $GLOBALS['settings']['proxy']) {
              $proxy = new \PHPWebDriver_WebDriverProxy();
              if (array_key_exists('proxy', $GLOBALS['settings']) && $GLOBALS['settings']['proxy.browsermob']) {
                  $this->client = new \PHPBrowserMobProxy_Client($GLOBALS['settings']['proxy']);
                  $proxy->httpProxy = $this->client->url;
                  $proxy->sslProxy = $this->client->url;
              } else {
                $proxy->httpProxy = $GLOBALS['settings']['proxy'];
                $proxy->sslProxy = $GLOBALS['settings']['proxy'];
              }
              $proxy->add_to_capabilities($additional_capabilities);
            }
        }

        $this->session = $this->driver->session($browser, $additional_capabilities);
        // var_dump($this->session);
                
        $this->sessionId = substr($this->session->getURL(), strrpos($this->session->getURL(), "/") + 1);
        // var_dump($this->sessionId);
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

    protected function parseAnnotations($docblock)
    {
        $annotations = array();

        if (preg_match_all('/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m', $docblock, $matches)) {
            $numMatches = count($matches[0]);

            for ($i = 0; $i < $numMatches; ++$i) {
                $annotations[$matches['name'][$i]][] = $matches['value'][$i];
            }
        }
        
        return $annotations;
    }

}
?>