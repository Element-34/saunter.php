<?php
namespace WebDriver;

include_once 'SaunterPHP/Framework/PO/WebDriver/Page.php';
include_once 'pages/TextArea.php';

class Editor extends SaunterPHP_Framework_PO_WebDriver_Page {
  private $locators = array(
    "iframe" => 'css=iframe',
  );

  function __construct($session) {
    parent::__construct($session);
  }
  
  // function __get($property) {
  //   switch($property) {
  //     case "results":
  //       return self::$session->getText($this->locators[$property]);
  //     default:
  //       return $this->$property;
  //   }
  // }
  
  function open() {
    self::$session->open($GLOBALS['settings']['webserver']);
    return $this;
  }
  
  function wait_until_loaded() {
    return $this;
  }
  
  function switch_to_textarea() {
    $iframe = self::$session->find_element_by_locator($this->locators["iframe"]);
    self::$session->frame($iframe);
    $t = new TextArea(self::$session);
    $t->wait_until_loaded();
    return $t;
  }
}