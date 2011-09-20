<?php

require_once 'Testing/Selenium.php';
require_once 'Testing/Selenium/Exception.php';

class SaunterPHP_Framework_Bindings_SaunterRemoteControl extends Testing_Selenium {
    public function __construct($browser, $url, $se_host, $se_port) {
        parent::__construct($browser, $url, $se_host, $se_port);
    }
    
    public function doCommand($verb, $args = array()) {
        $response = parent::doCommand($verb, $args);
        $this->is_ok($response);
        return $response;
    }

    public function is_ok($response) {
        if (substr($response, 0, 2) != "OK") {
            throw new Testing_Selenium_Exception('Non OK response from Selenium Server');
        }
    }
    
    public function as_number($response) {
        return (int) substr($response, 3);
    }
    
    public function getCssCount($css) {
        return $this->as_number($this->doCommand("getCssCount", array($css)));
    }
}


?>