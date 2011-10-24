<?php
/**
 * Saunter's WebDriver binding
 * 
 * @package SaunterPHP
 * @subpackage Framework_Bindings
 */

require_once 'PHPWebDriver/WebDriver.php';

class SaunterPHP_Framework_Bindings_SaunterWebDriver extends PHPWebDriver_WebDriver {
    public function __construct($executor = "") {
        parent::__construct($executor);
    }
}
?>