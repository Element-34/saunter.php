<?php
namespace RemoteControl;

include_once 'SaunterPHP/Framework/PO/RemoteControl/Page.php';
include_once 'pages/Preview.php';

class Upload extends SaunterPHP_Framework_PO_RemoteControl_Page {
  private $locators = array(
    'upload' => 'css=input[name="upload"]',
    'button' => 'css=input[type="submit"]',
    'storetime' => 'css=select[name="storetime"]',
    'obscure_filename' => 'css=select[name="addprivacy"]',
    'accept_rules' => 'css=select[name="rules"]',
  );

  function __construct() {
    parent::__construct();
  }
  
  function __set($property, $value) {
    switch($property) {
      case "upload":
        self::$selenium->attachFile($this->locators['upload'], $value);
        break;
      case "storetime":
      case "obscure_filename":
      case "accept_rules":
        self::$selenium->select($this->locators[$property], $value);
        break;
      default:
        $this->$property = $value;
    }
  }
  
  function open() {
    self::$selenium->open($GLOBALS['settings']['webserver']);
    return $this;
  }

  function wait_until_loaded() {
    $this->waitForElementAvailable($this->locators['upload']);
    return $this;
  }

  function upload($image, $storetime="30 Minutes", $obscure_filename="basic", $accept_rules="Yes") {
    $path_to_image = "{$GLOBALS['settings']['YourCompany']['file_server_base']}/files/$image";
  
    $this->upload = $path_to_image;
    $this->storetime = "label=$storetime";
    $this->obscure_filename = "label=$obscure_filename";
    $this->accept_rules = "label=$accept_rules";

    self::$selenium->click($this->locators['button']);
  
    $p = new Preview();
    $p->wait_until_loaded();
    return $p;
  }
}
?>