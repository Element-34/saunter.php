<?php
namespace WebDriver;

require_once 'tailored/EBayTestCase.php';
require_once 'pages/ShirtPage.php';

class BlacklistTest extends EBayTestCase {
    /**
    * @test
    * @group shallow
    * @group ebay
    * @group blacklist
    */
    public function collar_style() {
        $this->client->blacklist("http://www\\.facebook\\.com/.*", 200);
        $this->client->blacklist("http://static\\.ak\\.fbcdn\\.com/.*", 200);
        $sp = new ShirtPage($this->session);
        $sp->go_to_mens_dress_shirts();
        $sp->change_collar_style("Banded (Collarless)");
        $this->assertTrue($sp->is_collar_selected("Banded (Collarless)"));
        sleep(15);
    }

}
?>