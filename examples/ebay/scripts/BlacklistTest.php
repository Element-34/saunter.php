<?php
namespace WebDriver;

require_once 'tailored/EBayTestCase.php';
require_once 'pages/ShirtPage.php';
require_once 'PHPHARchive/HAR.php';

class BlacklistTest extends EBayTestCase {
    /**
    * @test
    * @group shallow
    * @group ebay
    * @group blacklist
    */
    public function collar_style() {
        $this->client->blacklist("http://www\\.facebook\\.com/.*", 306);
        $this->client->blacklist("http://static\\.ak\\.fbcdn\\.com/.*", 306);
        $sp = new ShirtPage($this->session);
        $this->client->new_har("shirts");
        $sp->go_to_mens_dress_shirts();
        sleep(5);
        $har = $this->client->har;
        
        // var_dump(json_encode($har));

        // $h = new \PHPHARchive_HAR($har);
        $h = new \PHPHARchive_HAR(dirname(__FILE__) . '/./shirts.json');
        $entries = $h->get_entries_by_page_ref("shirts");
        $three_oh_sixes = array();
        foreach ($entries as $entry) {
          if ($entry->response->status == 306) {
            array_push($three_oh_sixes, $entry);
          }
        }
        $this->assertEquals(count($three_oh_sixes), 1);
    }

}
?>