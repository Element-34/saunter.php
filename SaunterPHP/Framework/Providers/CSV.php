<?php
/**
 * @package SaunterPHP
 * @subpackage Providers
 */
require_once 'SaunterPHP/Framework/Exception.php';

class SaunterPHP_Framework_Providers_CSV {  
  function __construct($name) {
    if (is_file($GLOBALS['settings']['saunter.base'] . '/support/csv/' . $name)) {
      $this->handle = fopen($GLOBALS['settings']['saunter.base'] . '/support/csv/' . $name, "r");

      $headers = fgetcsv($this->handle);
      
      $this->data = array();
      while (($row = fgetcsv($this->handle)) !== FALSE) {
        array_push($this->data, array_combine($headers, $row));
      }
    } else {
      throw new Exception($GLOBALS['settings']['saunter.base'] . '/support/csv/' . $name . " does not exist");
    }
  }  

  function __destruct() {
    if ($this->handle) {
      fclose($this->handle); 
    }
  }

  // from http://www.php.net/manual/en/function.shuffle.php#99624
  private function shuffle_assoc($list) { 
    if (!is_array($list)) return $list; 

    $keys = array_keys($list); 
    shuffle($keys); 
    $random = array(); 
    foreach ($keys as $key) 
      $random[$key] = $list[$key]; 

    return $random; 
  }

  function random_row() {
    $shuffled = $this->shuffle_assoc($this->data);
    return $shuffled[array_rand($shuffled)];
  }

}
?>