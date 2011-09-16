<?php

require_once 'tailored/SauceTestCase.php';
require_once 'pages/LandingPage.php';

class LoginTest extends SauceTestCase {
  /**
  * @test
  * @group shallow
  * @group authentication
  */
  public function basic_page_object_example()
  {
    $landing = new LandingPage();
    $landing->open_default_base_url();
    $form = $landing->open_sign_in_form();
    $form->username = "monkey";
    $form->password = "buttress";
    $form->login(False);
    $this->verifyEquals($form->error_message, "Incorrect username or password.");
  }
}
?>