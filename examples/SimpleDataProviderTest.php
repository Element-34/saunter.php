<?php

require_once 'CustomTestCase.php';
require_once 'LandingPage.php';

class SimpleDataProvider extends CustomTestCase {
  public function static_provider() {
    return array(
     array("foo", "bslfsldkjflkdsjkjfsljljdar")
    );
  }
  
  /**
  * @test
  * @group shallow
  * @group authentication
  * @dataProvider static_provider
  */
  public function example_static_provider($username, $password)
  {
    $landing = new LandingPage();
    $landing->open_default_base_url();
    $form = $landing->open_sign_in_form();
    $form->username = $username;
    $form->password = $password;
    $form->login(False);
    $this->assertEquals($form->error_message, "Incorrect username or password.");
  }
}
?>