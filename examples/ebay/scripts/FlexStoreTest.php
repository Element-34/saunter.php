<?php
namespace WebDriver;

require_once 'tailored/EBayTestCase.php';
require_once 'pages/ShirtPage.php';
require_once 'PHPHARchive/HAR.php';

class FlexStoreTest extends EBayTestCase {
  /**
  * @test
  * @group flex
  */
  public function test_flex_click() {
    $this->session->open("http://localhost:8000/flexstore/flexstore.html");
    

  }

}
?>