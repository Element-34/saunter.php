<?php

require_once 'CustomTestCase.php';
require_once 'LandingPage.php';

class PageObjectExample extends CustomTestCase {
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