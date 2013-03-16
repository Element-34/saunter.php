<?php
namespace RemoteControl;

include_once 'SaunterPHP/Framework/PO/RemoteControl/Page.php';

class Preview extends SaunterPHP_Framework_PO_RemoteControl_Page {
  private $locators = array(
    'image' => 'css=.picture img[src^="/extpics"]:not([alt="new"])',
  );

  function __construct() {
    parent::__construct();
  }

  function wait_until_loaded() {
    $this->waitForElementAvailable($this->locators['image']);
    return $this;
  }

}
?>