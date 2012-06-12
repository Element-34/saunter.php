<?php
namespace RemoteControl;

require_once 'SaunterPHP/Framework/PO/RemoteControl/Page.php';
require_once 'pages/LoginPage.php';

class LandingPage extends SaunterPHP_Framework_PO_RemoteControl_Page {
  private $locators = array(
    "login" => 'css=[href="/login"][style]',
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
    self::$selenium->waitForPageToLoad('30000');
    $login_page = new LoginPage();
    $login_page->wait_until_loaded();
    return $login_page;
  }
}
?>