<?php
namespace RemoteControl;

require_once 'tailored/SauceTestCase.php';
require_once 'pages/LandingPage.php';
require_once 'SaunterPHP/Framework/Providers/CSV.php';

class ProviderTest extends SauceTestCase {
    /**
    * @test
    * @group shallow
    * @group authentication
    * @group provider
    */
    public function basic_csv_example() {
        $csv = new \SaunterPHP_Framework_Providers_CSV("invalid_usernames.csv");
        $row = $csv->random_row();
        $landing = new LandingPage();
        $landing->open_default_base_url();
        $form = $landing->open_sign_in_form();
        $form->username = $row["username"];
        $form->password = $row["password"];
        $form->login(False);
        $this->verifyEquals($form->error_message, "Incorrect username or password.");
    }
}
?>