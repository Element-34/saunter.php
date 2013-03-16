<?php
namespace WebDriver;

include_once 'SaunterPHP/Framework/PO/WebDriver/Page.php';

class Preview extends SaunterPHP_Framework_PO_WebDriver_Page {
  private $locators = array(
    'image' => array(\PHPWebDriver_WebDriverBy::CSS_SELECTOR, '.picture img[src^="/extpics"]:not([alt="new"])'),
  );

  function __construct($session) {
    parent::__construct($session);
  }

  function wait_until_loaded() {
    $w = new \PHPWebDriver_WebDriverWait(self::$session, $GLOBALS['timeouts']['seconds'], 0.5, array("locator" => $this->locators['image']));
    $w->until(
      function($session, $extra_arguments) {
        return call_user_func_array(array($session, "element"), $extra_arguments['locator']);
      }
    );
    return $this;
  }

}
?>