#!/usr/bin/env php

<?php

require_once 'SaunterPHP/Location.php';

$location = new SaunterPHP_Location();
$installed = $location->getLocation();

function initialize() {
    $defaults = $installed . '/Defaults';
    
    # conf
    if (! is_dir("conf")) {
        mkdir("conf");
    }
    copy($installed . "/conf/settings.inc.default", "conf/settings.inc.default");
    copy($installed . "/conf/saucelabs.inc.default", "conf/saucelabs.inc.default");
    copy($installed . "/phpunit.xml", "phpunit.xml");    

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

function copy_logfile(&$log_name) {
    copy("logs/" . $log_name . "xml", "logs/latest.xml");
}

if (in_array("--new", $argv)) {
    initialize();
    exit;
}

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

$log_name = date("y-m-d-H-m-s");
array_push($_SERVER['argv'], "--log-junit");
array_push($_SERVER['argv'], "logs/" . $log_name . "xml");
array_push($_SERVER['argv'], "scripts");

require_once 'PHP/CodeCoverage/Filter.php';
PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__, 'PHPUNIT');

if (strpos('/opt/local/bin/php', '@php_bin') === 0) {
    set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());
}

require_once 'PHPUnit/Autoload.php';

define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');

register_shutdown_function('copy_logfile', &$log_name);
PHPUnit_TextUI_Command::main();    

?>