<?php

include_once 'SaunterPHP/Framework/PO/WebDriver/Page.php';

class ShirtPage extends SaunterPHP_Framework_PO_WebDriver_Page {
  private $locators = array(
      "collar style" => 'css=a[title="REPLACE"]',
      "results" => 'css=.count',
      "throbber" => 'id=PreferenceThrob',
      "create new profile" => "id=prefB"
  );

  function __construct() {
    parent::__construct();
  }
  
  function __get($property) {
    switch($property) {
      case "results":
        return self::$driver->getText($this->locators[$property]);
      default:
        return $this->$property;
    }
  }
  
  function go_to_mens_dress_shirts() {
    self::$driver->open($GLOBALS['settings']['webserver'] . "/mens-clothing/Dress-Shirts/57991");
  }
  
  function change_collar_style($style) {
      $before = $this->results;
      self::$driver->click(str_replace("REPLACE", $style, $this->locators["collar style"]));
      $this->wait_for_value_changed($this->locators["results"], $before);
  }
  
  function is_collar_selected($collar) {
      if (self::$driver->isElementPresent(str_replace("REPLACE", $collar, $this->locators["collar style"]) . " .sl-deSel")) {
          return False;
      }
      return True;
  }
  
  function create_fashion_profile() {
      self::$driver->click($this->locators["create new profile"]);
      $w = new PHPWebDriver_WebDriverWait(self::$driver);
      $w->until(function($driver) {$driver->find_element_by_locator("id=overlayPanelProfileovolp-pad");});
  }
}
?>