<?php

require_once 'Testing/Selenium.php';
require_once 'SaunterPHP/Framework/Exception.php';

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
            throw new Saunter_Framework_Exception('Non OK response from Selenium Server: ' . $response);
        }
    }
    
    public function as_number($response) {
        return (int) substr($response, 3);
    }
    
    public function getCssCount($css) {
        return $this->as_number($this->doCommand("getCssCount", array($css)));
    }
    
    // the following methods will 'focus'
    public function click($locator) {
        $this->focus($locator);
        $this->doCommand("click", array($locator));
    }
    
    public function contextMenu($locator) {
        $this->focus($locator);
        $this->doCommand("contextMenu", array($locator));
    }
    
    public function clickAt($locator, $coordString) {
        $this->focus($locator);        
        $this->doCommand("clickAt", array($locator, $coordString));
    }
    
    public function doubleClickAt($locator, $coordString) {
        $this->focus($locator);    
        $this->doCommand("doubleClickAt", array($locator, $coordString));
    }
    
    public function contextMenuAt($locator, $coordString) {
        $this->focus($locator);    
        $this->doCommand("contextMenuAt", array($locator, $coordString));
    }
    
    public function keyPress($locator, $keySequence) {
        $this->focus($locator);
        $this->doCommand("keyPress", array($locator, $keySequence));
    }
    
    public function keyDown($locator, $keySequence) {
        $this->focus($locator);
        $this->doCommand("keyDown", array($locator, $keySequence));
    }
    
    public function keyUp($locator, $keySequence) {
        $this->doCommand("keyUp", array($locator, $keySequence));
    }
    
    public function mouseOut($locator) {
        $this->focus($locator);
        $this->doCommand("mouseOut", array($locator));
    }
    
    public function mouseDown($locator) {
        $this->focus($locator);
        $this->doCommand("mouseDown", array($locator));
    }
    
    public function mouseDownRight($locator) {
        $this->focus($locator);
        $this->doCommand("mouseDownRight", array($locator));
    }
    
    public function mouseDownAt($locator, $coordString) {
        $this->focus($locator);
        $this->doCommand("mouseDownAt", array($locator, $coordString));
    }
    
    public function mouseDownRightAt($locator, $coordString) {
        $this->focus($locator);
        $this->doCommand("mouseDownRightAt", array($locator, $coordString));
    }
    
    public function mouseUp($locator) {
        $this->focus($locator);
        $this->doCommand("mouseUp", array($locator));
    }
    
    public function mouseUpRight($locator) {
        $this->focus($locator);
        $this->doCommand("mouseUpRight", array($locator));
    }
    
    public function mouseUpAt($locator, $coordString) {
        $this->focus($locator);
        $this->doCommand("mouseUpAt", array($locator, $coordString));
    }
    
    public function mouseUpRightAt($locator, $coordString) {
        $this->focus($locator);
        $this->doCommand("mouseUpRightAt", array($locator, $coordString));
    }
    
    public function mouseMove($locator) {
        $this->focus($locator);
        $this->doCommand("mouseMove", array($locator));
    }
    
    public function mouseMoveAt($locator, $coordString) {
        $this->focus($locator);
        $this->doCommand("mouseMoveAt", array($locator, $coordString));
    }
    
    public function type($locator, $value) {
        $this->focus($locator);
        $this->doCommand("type", array($locator, $value));
    }
    
    public function typeKeys($locator, $value) {
        $this->focus($locator);
        $this->doCommand("typeKeys", array($locator, $value));
    }
    
    public function check($locator) {
        $this->focus($locator);
        $this->doCommand("check", array($locator));
    }
    
    public function uncheck($locator) {
        $this->focus($locator);
        $this->doCommand("uncheck", array($locator));
    }
    
    public function select($selectLocator, $optionLocator) {
        $this->focus($selectLocator);
        $this->doCommand("select", array($selectLocator, $optionLocator));
    }
    
    public function addSelection($locator, $optionLocator) {
        $this->focus($locator);        
        $this->doCommand("addSelection", array($locator, $optionLocator));
    }
    
    public function removeSelection($locator, $optionLocator) {
        $this->focus($locator);
        $this->doCommand("removeSelection", array($locator, $optionLocator));
    }
    
    public function removeAllSelections($locator) {
        $this->focus($locator);
        $this->doCommand("removeAllSelections", array($locator));
    }
    
}

?>