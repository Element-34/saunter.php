<?php

require_once 'BasePage.php';
require_once 'LoginPage.php';

class LandingPage extends BasePage {
  private $locators = array(
    "login" => "css=div.account_mast a:first",
    "signup" => "css=div.account_mast a:last"
  );

  function __construct() {
    $this->selenium = SeleniumConnection::getInstance()->selenium;
  }
  
  function open_default_base_url() {
    $this->selenium->open("/");
  }
  
  function open_sign_in_form() {
    $this->selenium->click($this->locators['login']);
    $login_page = new LoginPage();
    $login_page->wait_until_loaded();
    return $login_page;
  }
}
?>