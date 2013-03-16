<?php
namespace WebDriver;

require_once 'tailored/PicPasteTestCase.php';
require_once 'pages/Upload.php';
require_once 'PHPHARchive/HAR.php';

class FileUploadTest extends PicPasteTestCase {
    public function setUp() {
        parent::setUp();

        $this->u = new Upload($this->session);
        $this->u->open()->wait_until_loaded();
    }

    public function tearDown() {
        parent::tearDown();
    }

    /**
    * @test
    * @group shallow
    */
    public function local_upload() {
        $this->u->upload('english_muffin.jpg');
    }

}
?>