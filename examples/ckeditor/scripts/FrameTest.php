<?php
namespace WebDriver;

require_once 'SaunterPHP/Framework/TestCase/WebDriver.php';
require_once 'pages/Editor.php';

class FrameTest extends SaunterPHP_Framework_SaunterTestCase {
    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();        
    }

    /**
    * @test
    * @group deep
    * @group ck
    * @group frame
    */
    public function number_of_paragraphs() {
        $e = new Editor($this->session);
        $e->open()->wait_until_loaded();
        $t = $e->switch_to_textarea();
        $this->assertEquals($t->how_many_paragraphs(), 6);
    }
}