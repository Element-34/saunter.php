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
        $sp = new ShirtPage($this->session);
        $sp->go_to_mens_dress_shirts();
        $sp->change_collar_style("Banded (Collarless)");
        $this->assertTrue($sp->is_collar_selected("Banded (Collarless)"));
    }

    /**
    * @test
    * @group shallow
    * @group ebay
    * @group adam
    */
    public function fashion_profile() {
        $sp = new ShirtPage($this->session);
        $sp->go_to_mens_dress_shirts();
        $sp->create_fashion_profile();
    }
}
?>