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
        $sp->go_to_mens_dress_shirts();
        $sp->change_collar_style("Banded (Collarless)");
        $this->assertTrue($sp->is_collar_selected("Banded (Collarless)"));
    }
}
?>