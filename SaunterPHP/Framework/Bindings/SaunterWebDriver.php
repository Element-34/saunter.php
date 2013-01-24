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
    
    public function session($browser, $additional_capabilities = array(), $curl_opts = array()) {
        $desired_capabilities = array_merge(
          $additional_capabilities,
          array('browserName' => $browser));

        $results = $this->curl(
          'POST',
          '/session',
          array('desiredCapabilities' => $desired_capabilities),
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