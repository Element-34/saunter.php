#!/usr/bin/env php

<?php

require_once 'SaunterPHP/Location.php';

$location = new SaunterPHP_Location();
$installed = $location->getLocation();

function initialize($installed) {
    $defaults = $installed . '/Defaults';
    
    # conf
    if (! is_dir("conf")) {
        mkdir("conf");
    }
    copy($defaults . "/conf/saunter.inc.default", "conf/settings.inc.default");
    copy($defaults . "/conf/saucelabs.inc.default", "conf/saucelabs.inc.default");

    copy($defaults . "/phpunit.xml", "phpunit.xml");
    reinitialize($installed);
    
    if (! is_dir("scripts")) {
        mkdir("scripts");
    }

    if (! is_dir("includes")) {
        mkdir("includes");
    }
    
    if (! is_dir("includes/pages")) {
        mkdir("includes/pages");
    }

    if (! is_dir("includes/providers")) {
        mkdir("includes/providers");
    }

    if (! is_dir("includes/tailored")) {
        mkdir("includes/tailored");
    }
    
    if (! is_dir("logs")) {
        mkdir("logs");
    }
}

function reinitialize($installed) {
    $status_listener = $installed . '/Framework/Listeners/StatusListener.php';
    $xml = simplexml_load_file('phpunit.xml');

    foreach ($xml->listeners->listener as $listener) {
        if ($listener['class'] == 'SaunterPHP_Framework_Listeners_StatusListener') {
            if ($listener['file'] != $status_listener) {
                $dom = new DOMDocument('1.0');
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                $listener['file'] = $status_listener;
                $dom->loadXML($xml->asXML());
                $dom->save('phpunit.xml');
            }
        }
    }
}

function copy_logfile(&$log_name) {
    copy("logs/" . $log_name . ".xml", "logs/latest.xml");
}

if (in_array("--new", $argv)) {
    initialize($installed);
    exit;
}

if (in_array("--reset", $argv)) {
    reinitialize($installed);
    exit;
}

$log_name = date("y-m-d-H-m-s");
array_push($_SERVER['argv'], "--log-junit");
array_push($_SERVER['argv'], "logs/" . $log_name . ".xml");
array_push($_SERVER['argv'], "scripts");

register_shutdown_function('copy_logfile', &$log_name);

require_once 'conf/saunter.inc';

/****
 * the code in this block is covered under the Creative Commons Attribution 3.0 Unported License
 * from phpunit that can be read at http://www.phpunit.de/manual/current/en/appendixes.copyright.html
 */

if (strpos('@php_bin@', '@php_bin') === 0) {
   require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'PHPUnit' . DIRECTORY_SEPARATOR . 'Autoload.php';
} else {
   require '@php_dir@' . DIRECTORY_SEPARATOR . 'PHPUnit' . DIRECTORY_SEPARATOR . 'Autoload.php';
}

define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');

PHPUnit_TextUI_Command::main();    
/*
 * end phpunit licensed block
 ****/

?>