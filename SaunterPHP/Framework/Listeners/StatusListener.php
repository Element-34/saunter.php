<?php
/**
 * @package SaunterPHP
 * @subpackage Framework_Listeners
 */
 
class SaunterPHP_Framework_Listeners_StatusListener implements PHPUnit_Framework_TestListener
{
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time) {
        $test->execution_status = PHPUnit_Runner_BaseTestRunner::STATUS_ERROR;
    }
 
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time) {
        $test->execution_status = PHPUnit_Runner_BaseTestRunner::STATUS_FAILURE;
    }
 
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
        $test->execution_status = PHPUnit_Runner_BaseTestRunner::STATUS_INCOMPLETE;
    }
 
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
        $test->execution_status = PHPUnit_Runner_BaseTestRunner::STATUS_SKIPPED;
    }
 
    public function startTest(PHPUnit_Framework_Test $test) {}
    
    public function endTest(PHPUnit_Framework_Test $test, $time) {
        if (property_exists($test, "sessionId")) {
            // fetching the stuff from the server doesn't require the connection anymore
            if (property_exists($test, "selenium")) {
                if (method_exists($test::$selenium, "stop")) {
                    $test::$selenium->stop();                
                }
            }
            if (property_exists($test, "session")) {
                if (method_exists($test->session, "close")) {
                    $test->session->close();                
                }
            }
            
            if ($GLOBALS['settings']['sauce.ondemand']) {
                // job name
                $context = array("name" => $test->getName());

                // job result
                if ($test->getStatus() == PHPUnit_Runner_BaseTestRunner::STATUS_PASSED) {
                    $context["passed"] = True;
                } else {
                    $context["passed"] = False;
                }

                // job tags
                $reflector = new ReflectionMethod($test, $test->getName());
                preg_match_all("(@group .*)", $reflector->getDocComment(), $raw_tags);
                if (count($raw_tags[0]) > 0) {
                    $context["tags"] = array();
                    foreach ($raw_tags[0] as $raw_tag) {
                        $split_tag = explode(" ", $raw_tag);
                        array_push($context["tags"], $split_tag[1]);
                    }
                }

                // global custom data
                $custom_data = array();
                if (array_key_exists('saucelabs', $GLOBALS) && array_key_exists('custom-data', $GLOBALS['saucelabs'])) {
                    foreach ($GLOBALS['saucelabs']['custom-data'] as $key => $value) {
                        $custom_data[$key] = $value;
                    }
                }
                $context['custom-data'] = $custom_data;

                $body = json_encode($context);
                $fp = fopen('php://temp/maxmemory:256000', 'w');
                if (!$fp) {
                    die('could not open temp memory data');
                }
                fwrite($fp, $body);
                fseek($fp, 0);
                
                $sauce_rest_handle = curl_init();
                curl_setopt($sauce_rest_handle, CURLOPT_USERPWD,  $GLOBALS['saucelabs']['username'] . ":" . $GLOBALS['saucelabs']['key']);
                curl_setopt($sauce_rest_handle, CURLOPT_URL, "https://saucelabs.com/rest/v1/" . $GLOBALS['saucelabs']['username'] . "/jobs/" . $test->sessionId);
                curl_setopt($sauce_rest_handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
                curl_setopt($sauce_rest_handle, CURLOPT_PUT, 1);
                curl_setopt($sauce_rest_handle, CURLOPT_INFILE, $fp);
                curl_setopt($sauce_rest_handle, CURLOPT_INFILESIZE, strlen($body));
                curl_setopt($sauce_rest_handle, CURLOPT_RETURNTRANSFER, True);
                $result = curl_exec($sauce_rest_handle);
                curl_close($sauce_rest_handle);
            }

            if ($GLOBALS['settings']['sauce.ondemand'])
            {
                if ($GLOBALS['settings']['sauce.get_video'])
                {
                    $sauce_rest_handle = curl_init();
                    curl_setopt($sauce_rest_handle, CURLOPT_USERPWD,  $GLOBALS['saucelabs']['username'] . ":" . $GLOBALS['saucelabs']['key']);
                    curl_setopt($sauce_rest_handle, CURLOPT_URL, "https://saucelabs.com/rest/" . $GLOBALS['saucelabs']['username'] . "/jobs/" . $test->sessionId . "/results/video.flv");
                    $video = fopen("logs/video.flv", "w");
                    $code = 404;
                    while ($code == 404) {
                        curl_setopt($sauce_rest_handle, CURLOPT_FILE, $video);
                        curl_exec($sauce_rest_handle);
                        $info = curl_getinfo($sauce_rest_handle);
                        $code = $info['http_code'];
                        if ($code == 404) {
                            sleep(1);
                      }
                    }
                    curl_close($sauce_rest_handle);
                }

                if ($GLOBALS['settings']['sauce.get_log'])
                {
                    $sauce_rest_handle = curl_init();
                    curl_setopt($sauce_rest_handle, CURLOPT_USERPWD,  $GLOBALS['saucelabs']['username'] . ":" . $GLOBALS['saucelabs']['key']);
                    curl_setopt($sauce_rest_handle, CURLOPT_URL, "https://saucelabs.com/rest/" . $GLOBALS['saucelabs']['username'] . "/jobs/" . $test->sessionId . "/results/selenium-server.log");
                    $video = fopen("logs/sauce-selenium-server.log", "w");
                    $code = 404;
                    while ($code == 404) {
                        curl_setopt($sauce_rest_handle, CURLOPT_FILE, $video);
                        curl_exec($sauce_rest_handle);
                        $info = curl_getinfo($sauce_rest_handle);
                        $code = $info['http_code'];
                        if ($code == 404) {
                            sleep(1);
                        }
                    }
                    curl_close($sauce_rest_handle);
                }
            }
        }
    }

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite) {}
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite) {}
}

?>