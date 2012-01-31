<?php
/**
 * @package SaunterPHP
 * @subpackage Framework_PO_WebDriver
 */
namespace WebDriver;

require_once 'SaunterPHP/Framework/Exception.php';
include_once 'PHPWebDriver/WebDriverWait.php';

class SaunterPHP_Framework_PO_WebDriver_Page {
  
    public static $string_timeout;
    public static $session;

    // constructor
    function __construct($session) {
        self::$string_timeout = $GLOBALS['timeouts']["str_ms"];
        self::$session = $session;
    }  

    function __destruct() {
   
    }

    function get_all_meta_tags($custom = array()) {
      $html401 = array("lang", "dir", "http-equiv", "name", "content", "scheme");
      $html5 = array("charset");
      $attributes = array_merge($html401, $html5, $custom);

      $bin = array();

      $metas = self::$session->find_elements_by_locator("tag=meta");
      foreach ($metas as $meta) {
        $bucket = array();
        foreach ($attributes as $attribute) {
          $fetched = $meta->attribute($attribute);
          if ($fetched) {
            $bucket[$attribute] = $fetched;
          }
        }
        $bin[] = $bucket;
      }
      return $bin;
    }

    function get_named_meta_tag($named, $custom = array()) {
      $metas = $this->get_all_meta_tags($custom);
      foreach ($metas as $meta) {
        if (array_key_exists("name", $meta) && $meta["name"] == $named) {
          return $meta;        
        }
      }
    }

    function get_css_attribute_from_element($element, $attribute) {
        return $element->css($attribute);
    }
    
    function wait_for_value_changed($where, $what) {
        $element = self::$session->find_element_by_locator($where);
        foreach (range(0, $GLOBALS['timeouts']["seconds"]) as $value) {
            try {
                $text = $element->text();
                if ((strlen(trim($text)) != 0) && trim($text) != $what) {
                    return True;
                }
            } catch (\PHPWebDriver_ObsoleteElementWebDriverError $e) {
                $element = self::$session->find_element_by_locator($where);
            }
            sleep(1);
        }
        throw new \SaunterPHP_Framework_TimeoutError(
          sprintf(
            'Element with locator "%s" timed out waiting for it not to be "%s"',
            $where,
            $what));
    }
    
    function wait_for_element_available($locator, $timeout = null) {
        if (! $timeout) {
          $timeout = $GLOBALS['timeouts']['seconds'];
        }
        
        $w = new \PHPWebDriver_WebDriverWait(self::$session, $timeout);
        $before = time();
        $w->until(
          create_function('$session', 
            sprintf(
              '$got = $session->find_elements_by_locator("%s");
                if (count($got) > 0) {
                  return $got;
                } else {
                  return false;
                }'
            , addslashes($locator))
          )
        );
        $remaining = ($before + $timeout) - time();
        if ($remaining > 0) {
          $w->until(
            create_function('$session', 
              sprintf(
                '$got = $session->find_element_by_locator("%s");
                if ($got->displayed()) {
                  return $got; 
                } else {
                  return false;
                }'
              , addslashes($locator))
            )
          );
        }
    }
}

?>