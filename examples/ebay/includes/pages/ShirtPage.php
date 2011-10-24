<?php

require_once 'SaunterPHP/Framework/PO/WebDriver/Page.php';

class ShirtPage extends SaunterPHP_Framework_PO_WebDriver_Page {
  private $locators = array(
      "collar style" => 'css=a[title="REPLACE"]',
      "results" => 'css=.count',
      "throbber" => 'id=PreferenceThrob'
  );

  function __construct() {
    parent::__construct();
  }
  
  function open_page_url() {
    self::$driver->open($GLOBALS['settings']['webserver'] . "/mens-clothing/Dress-Shirts/57991");
  }
  
  function open_sign_in_form() {
    self::$driver->click($this->locators['login']);
    $login_page = new LoginPage();
    $login_page->wait_until_loaded();
    return $login_page;
  }
}
?>