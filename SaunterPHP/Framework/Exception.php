<?php
/**
 * @package SaunterPHP
 */
require_once 'PHPUnit/Framework/Exception.php';

class SaunterPHP_Framework_Exception extends PHPUnit_Framework_Exception {
  public function __construct($message) {
    parent::__construct($message);
  }

}

class SaunterPHP_Framework_NoSuchLocationStrategy extends SaunterPHP_Framework_Exception {}
class SaunterPHP_Framework_TimeoutError extends SaunterPHP_Framework_Exception {}

?>