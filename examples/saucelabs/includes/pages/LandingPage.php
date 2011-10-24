<?php

require_once 'SaunterPHP/Framework/PO/RemoteControl/Page.php';
require_once 'pages/LoginPage.php';

class LandingPage extends SaunterPHP_Framework_PO_RemoteControl_Page {
  private $locators = array(
    "login" => "css=div.account_mast a:first",
    "signup" => "css=div.account_mast a:last"
  );

  function __construct() {
    parent::__construct();
  }
  
  function open_default_base_url() {
    self::$selenium->open("/");
  }
  
  function open_sign_in_form() {
    self::$selenium->click($this->locators['login']);
    $login_page = new LoginPage();
    $login_page->wait_until_loaded();
    return $login_page;
  }
}
?>