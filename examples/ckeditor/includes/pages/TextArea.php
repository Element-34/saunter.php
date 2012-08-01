<?php
namespace WebDriver;

include_once 'SaunterPHP/Framework/PO/WebDriver/Page.php';
include_once 'pages/Editor.php';

class TextArea extends SaunterPHP_Framework_PO_WebDriver_Page {
  private $locators = array();

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
  
  function wait_until_loaded() {
    return $this;
  }
  
  function switch_to_editor() {
    self::$session->frame();
    $e = new Editor($this->session);
    $e->wait_until_loaded();
    return $e;
  }
  
  function how_many_paragraphs() {
    return count(self::$session->find_elements_by_locator('css=p'));
  }
}