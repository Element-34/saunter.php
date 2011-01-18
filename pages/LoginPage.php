<?php

require_once 'BasePage.php';
require_once 'SeleniumConnection.php';

class LoginPage extends BasePage {
  private $locators = array(
    "username" => "username",
    "password" => "password",
    "submit_button" => "submit",
    "error_message" => "css=div.error > p"
  );

  function __construct() {
    $this->selenium = SeleniumConnection::getInstance()->selenium;
  }
  
  function __set($property, $value) {
    switch($property) {
      // cases can be stacked so all the 'text' ones here
      case "username":
      case "password":
        $this->selenium->type($this->locators[$property], $value);
        break;
      // if there were other types of elements like checks and selects
      // there would be another stack of cases here
      default:
        $this->$property = $value;
    }
  }
  
  function __get($property) {
    switch($property) {
      case "error_message":
        return $this->selenium->getText($this->locators[$property]);
      default:
        return $this->$property;
    }
  }
  
  function wait_until_loaded() {
    $this->waitForElementAvailable($this->locators['username']);
  }

  function login() {
    $this->selenium->click($this->locators['submit_button']);
    $this->selenium->waitForPageToLoad(parent::$string_timeout);
  }
}

?>