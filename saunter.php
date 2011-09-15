#!/usr/bin/env php

<?php

require_once 'SaunterPHP/Defaults/Location.php';

function initialize() {
    $defaults = new SaunterPHP_Defaults_Location();
    $installed = $defaults->getDefaultsLocation();
    
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
}

?>