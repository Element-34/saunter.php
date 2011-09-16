<?php

require_once 'SaunterPHP/Framework/PO/RemoteControl/Page.php';

class LoginPage extends SaunterPHP_Framework_PO_RemoteControl_Page {
  private $locators = array(
    "username" => "username",
    "password" => "password",
    "submit_button" => "submit",
    "error_message" => "css=div.error p:nth(0)"
  );

  function __construct() {
    parent::__construct();
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

  function login($should_pass = True) {
    $this->selenium->click($this->locators['submit_button']);
    $this->selenium->waitForPageToLoad(parent::$string_timeout);
    if ($should_pass) {
      // load and return the account object
    } else {
      $this->waitForElementAvailable($this->locators['error_message']);      
    }
  }
}

?>