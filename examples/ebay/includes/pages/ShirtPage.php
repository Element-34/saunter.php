<?php
namespace WebDriver;

include_once 'SaunterPHP/Framework/PO/WebDriver/Page.php';

class ShirtPage extends SaunterPHP_Framework_PO_WebDriver_Page {
  private $locators = array(
      "collar style" => 'css=a[title="REPLACE"]',
      "results" => 'id=v4-p311',
      "throbber" => 'id=PreferenceThrob',
      "create new profile" => "id=prefB"
  );

  function __construct($session) {
    parent::__construct($session);
  }
  
  function __get($property) {
    switch($property) {
      case "results":
        return self::$session->getText($this->locators[$property]);
      default:
        return $this->$property;
    }
  }
  
  function go_to_mens_dress_shirts() {
    self::$session->open($GLOBALS['settings']['webserver'] . "/mens-clothing/Dress-Shirts/57991");
  }
  
  function change_collar_style($style) {
      self::$session->click(str_replace("REPLACE", $style, $this->locators["collar style"]));
  }
  
  function is_collar_selected($collar) {
      if (self::$session->isElementPresent(str_replace("REPLACE", $collar, $this->locators["collar style"]) . " .sl-deSel")) {
          return False;
      }
      return True;
  }
  
  function create_fashion_profile() {
      self::$session->click($this->locators["create new profile"]);
      $w = new \PHPWebDriver_WebDriverWait(self::$session, 5);
      $w->until(function($driver) {$driver->find_element_by_locator("id=overlayPanelProfileovolp-pad");});
  }
}
?>