<?php

require_once 'CustomTestCase.php';
require_once 'landingPage.php';

class PageObjectExample extends CustomTestCase {
  /**
  * @test
  */
  public function example()
  {
    $landing = new LandingPage();
    $landing->open_default_base_url();
    $form = $landing->open_sign_in_form();
    $form->username = "monkey";
    $form->password = "buttress";
    $form->login();
    $this->assertEquals($form->error_message, "Incorrect username or password.");
    sleep(3);
  }
}
?>