<?php
/**
 * Saunter's WebDriver binding
 * 
 * @package SaunterPHP
 * @subpackage Framework_Bindings
 */

require_once 'PHPWebDriver/WebDriver.php';

class SaunterPHP_Framework_Bindings_SaunterWebDriver extends PHPWebDriver_WebDriver {
    public function __construct($executor = "") {
        parent::__construct($executor);
    }
    
    public function session($browser = 'unknown browser', $additional_capabilities = array(), $curl_opts = array(), $browser_profile = null) {
        if ($browser == 'unknown browser') {
            throw new SaunterPHP_Framework_Exception("Pretty sure you can't even get here...");
        }

        $desired_capabilities = new PHPWebDriver_WebDriverDesiredCapabilities();

        $decoded = json_decode($GLOBALS['settings']['browser'], true);
        if ($decoded) {
            $browser_string = $decoded["browser"];
            $default_capabilities = array_merge(
                $desired_capabilities->$browser_string,
                $decoded
            );
        } else {
            $browser_string = $GLOBALS['settings']['browser'];
            $default_capabilities = $desired_capabilities->$browser_string;
        }

        if ($browser_string == "firefox" && $browser_profile) {
            $additional_capabilities['firefox_profile'] = $browser_profile->encoded();
        }

        $final_capabilities = array_merge(
            $default_capabilities,
            $additional_capabilities
        );

        $results = $this->curl(
          'POST',
          '/session',
          array('desiredCapabilities' => $final_capabilities),
          array(CURLOPT_FOLLOWLOCATION => true));

        return new SaunterPHP_Framework_Bindings_SaunterWebDriverSession($results['info']['url']);
    }
}

class SaunterPHP_Framework_Bindings_SaunterWebDriverSession extends PHPWebDriver_WebDriverSession {
    public function __construct($url) {
        parent::__construct($url);
    }

    public function find_element_by_locator($locator_string) {
        $strategy = substr($locator_string, 0, strpos($locator_string, "="));
        $locator = substr($locator_string, strpos($locator_string, "=") + 1);
        
        switch($strategy) {
            case "class":
                return $this->element("class name", $locator);
            case "css":
                return $this->element("css selector", $locator);
            case "id":
                return $this->element("id", $locator);
            case "name":
                return $this->element("name", $locator);
            case "link":
                return $this->element("link text", $locator);
            case "plink":
                return $this->element("partial link text", $locator);
            case "tag":
                return $this->element("tag name", $locator);
            case "xpath":
                return $this->element("xpath", $locator);
            default:
              throw new SaunterPHP_Framework_NoSuchLocationStrategy($strategy . ' is not a valid location strategy');
        }
    }

    public function find_elements_by_locator($locator_string) {
        $strategy = substr($locator_string, 0, strpos($locator_string, "="));
        $locator = substr($locator_string, strpos($locator_string, "=") + 1);
        
        switch($strategy) {
            case "class":
                return $this->elements("class name", $locator);
            case "css":
                return $this->elements("css selector", $locator);
            case "id":
                return $this->elements("id", $locator);
            case "name":
                return $this->elements("name", $locator);
            case "link":
                return $this->elements("link text", $locator);
            case "plink":
                return $this->elements("partial link text", $locator);
            case "tag":
                return $this->elements("tag name", $locator);
            case "xpath":
                return $this->elements("xpath", $locator);
            default:
              throw new SaunterPHP_Framework_NoSuchLocationStrategy($strategy . ' is not a valid location strategy');
        }
    }

    public function getText($locator_string) {
        $element = $this->find_element_by_locator($locator_string);
        return $element->text();
    }
    
    public function isElementPresent($locator_string) {
        $elements = $this->find_elements_by_locator($locator_string);
        if (count($elements) == 0) {
            return False;
        }
        return True;
    }
}

?>