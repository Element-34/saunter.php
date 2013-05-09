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
    copy($defaults . "/conf/saunter.inc.default", "conf/saunter.inc.default");
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

    if (! is_dir("support")) {
        mkdir("support");
    }

    if (! is_dir("support/csv")) {
        mkdir("support/csv");
    }
}

function reinitialize($installed) {
    $has_changes = false;

    $status_listener = $installed . '/Framework/Listeners/StatusListener.php';
    $xml = simplexml_load_file('phpunit.xml');

    // get the status listener
    foreach ($xml->listeners->listener as $listener) {
        if ($listener['class'] == 'SaunterPHP_Framework_Listeners_StatusListener') {
            if ($listener['file'] != $status_listener) {
                $listener['file'] = $status_listener;
                $has_changes = true;
            }
        }
    }

    // includepath is platform specific
    $os = strtolower(PHP_OS);
    $current = $xml->php->includePath;
    if (substr($os, 0, 3) == 'win') {
        if (strpos($current, '/') !== false) {
            $xml->php->includePath = str_replace('/', '\\', $xml->php->includePath);
            $has_changes = true;
        }
        if (strpos($current, ':') !== false) {
            $xml->php->includePath = str_replace(':', ';', $xml->php->includePath);
            $has_changes = true;
        }
    } else {
        if (strpos($current, '\\') !== false) {
            $xml->php->includePath = str_replace('\\', '/', $xml->php->includePath);
            $has_changes = true;
        }
        if (strpos($current, ';') !== false) {
            $xml->php->includePath = str_replace(';', ':', $xml->php->includePath);
            $has_changes = true;
        }
    }

    if ($has_changes) {
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $dom->save('phpunit.xml');
    }
}

function copy_logfile($log_name) {
    copy($log_name, "logs/latest.xml");
}

function mix_in_attachments($log_name) {
    // load the xml
    $xml = simplexml_load_file($log_name);

    // find all the logs
    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($GLOBALS['settings']['logdir'], $flags = \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $key=>$value) {
        if ((substr($key, -strlen('test.log')) === 'test.log')) {
            // iterate for our magic token
            $lines = file($key);
            foreach ($lines as $line_number => $line) {
                if (!strncmp($line, '[[ATTACHMENT', strlen('[[ATTACHMENT'))) {
                    // get the attachment path
                    $attachment_path = substr($line, strlen('[[ATTACHMENT|'), -3);

                    // chomp things into bits
                    $parts = explode(DIRECTORY_SEPARATOR, substr($line, 0, -3));

                    $class = $parts[count($parts) -3];
                    $method = $parts[count($parts) - 2];

                    // find our method entry
                    $case_elements = $xml->xpath("(//testcase[@name=\"$method\"])");
                    foreach ($case_elements as $case_element) {
                        $attributes = $case_element->attributes();
                        if ((substr((string)$attributes['class'], -strlen($class)) === $class)) {
                            // already have stdout?
                            $stdout = $case_element->xpath('./stdout');
                            if (count($stdout) == 0) {
                                $stdout = $case_element->addChild('stdout', PHP_EOL . $line . PHP_EOL);
                            } else {
                                $existing = (string)$stdout[0];
                                $case_element->stdout = $existing . $line . PHP_EOL;
                            }
                        }
                    }
                }
            }
        }
    }

    // write it out again
    $xml->asXML($log_name);
}

if (in_array("--new", $argv)) {
    initialize($installed);
    exit;
}

if (in_array("--reset", $argv)) {
    reinitialize($installed);
    exit;
}

require_once 'conf/saunter.inc';

$GLOBALS['settings']['saunter.base'] = getcwd();

$timestamp = date('Y-m-d-h-i-s');
$GLOBALS['settings']['logdir'] = $GLOBALS['settings']['saunter.base'] . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $timestamp;
mkdir($GLOBALS['settings']['logdir']);
$GLOBALS['settings']['logname'] = $GLOBALS['settings']['logdir'] . DIRECTORY_SEPARATOR . $timestamp . '.xml';

array_push($_SERVER['argv'], "--log-junit");
array_push($_SERVER['argv'], $GLOBALS['settings']['logname']);
array_push($_SERVER['argv'], "scripts");

// called in order
if (array_key_exists('saunter.ci', $GLOBALS['settings']) && $GLOBALS['settings']['saunter.ci'] == 'jenkins') {
    register_shutdown_function('mix_in_attachments', $GLOBALS['settings']['logname']);
}
register_shutdown_function('copy_logfile', $GLOBALS['settings']['logname']);

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