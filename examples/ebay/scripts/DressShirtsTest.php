<?php

require_once 'tailored/EBayTestCase.php';
require_once 'pages/ShirtPage.php';

class DressShirtTest extends EBayTestCase {
    /**
    * @test
    * @group shallow
    * @group ebay
    */
    public function collar_style() {
        $sp = new ShirtPage();
        $sp->open_page_url();
    }
}
?>