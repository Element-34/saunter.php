<?php
namespace WebDriver;

include_once 'SaunterPHP/Framework/PO/WebDriver/Page.php';
include_once 'pages/Preview.php';
require_once('PHPWebDriver/WebDriverBy.php');
require_once('PHPWebDriver/Support/WebDriverSelect.php');

class Upload extends SaunterPHP_Framework_PO_WebDriver_Page {
  private $locators = array(
    'upload' => array(\PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'input[name="upload"]'),
    'button' => array(\PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'input[type="submit"]'),
    'storetime' => array(\PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'select[name="storetime"]'),
    'obscure_filename' => array(\PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'select[name="addprivacy"]'),
    'accept_rules' => array(\PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'select[name="rules"]'),
  );

  function __construct($session) {
    parent::__construct($session);
  }
  
  function __set($property, $value) {
    switch($property) {
      case "upload":
        $e = call_user_func_array(array(self::$session, "element"), $this->locators[$property]);
        $e->sendKeys($value);
        break;
      case "storetime":
      case "obscure_filename":
      case "accept_rules":
        $e = call_user_func_array(array(self::$session, "element"), $this->locators[$property]);
        $s = new \PHPWebDriver_Support_WebDriverSelect($e);
        $s->select_by_visible_text($value);
        break;
      default:
        $this->$property = $value;
    }
  }
  
  function open() {
    self::$session->open($GLOBALS['settings']['webserver']);
    return $this;
  }
  
  function wait_until_loaded() {
    $w = new \PHPWebDriver_WebDriverWait(self::$session, $GLOBALS['timeouts']['seconds'], 0.5, array("locator" => $this->locators['upload']));
    $w->until(
      function($session, $extra_arguments) {
        return call_user_func_array(array($session, "element"), $extra_arguments['locator']);
      }
    );
    return $this;
  }

  function upload($image, $storetime="30 Minutes", $obscure_filename="basic", $accept_rules="Yes") {
    $path_to_image = "{$GLOBALS['settings']['saunter.base']}/support/files/$image";
  
    $this->upload = $path_to_image;
    $this->storetime = $storetime;
    $this->obscure_filename = $obscure_filename;
    $this->accept_rules = $accept_rules;

    $b = call_user_func_array(array(self::$session, "element"), $this->locators['button']);
    $b->click();
  
    $p = new Preview(self::$session);
    $p->wait_until_loaded();
    return $p;
  }
}
?>