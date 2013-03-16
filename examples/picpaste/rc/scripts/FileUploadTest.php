<?php
namespace RemoteControl;

require_once 'tailored/PicPasteTestCase.php';
require_once 'pages/Upload.php';

class FileUploadTest extends PicPasteTestCase {
    public function setUp() {
        parent::setUp();

        $this->u = new Upload();
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