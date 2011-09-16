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

if (in_array("--new", $argv)) {
    initialize();
    exit;
}

$custom_listeners = $installed . '/Framework/Listeners';
set_include_path(get_include_path() . PATH_SEPARATOR . $custom_listeners);
array_push($_SERVER['argv'], "--log-junit");
array_push($_SERVER['argv'], "logs/foo.xml");
array_push($_SERVER['argv'], "scripts");

require_once 'PHP/CodeCoverage/Filter.php';
PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__, 'PHPUNIT');

if (strpos('/opt/local/bin/php', '@php_bin') === 0) {
    set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());
}

require_once 'PHPUnit/Autoload.php';

define('PHPUnit_MAIN_METHOD', 'PHPUnit_TextUI_Command::main');

PHPUnit_TextUI_Command::main();

?>