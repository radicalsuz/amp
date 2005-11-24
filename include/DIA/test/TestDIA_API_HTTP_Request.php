<?php
if(!defined('DIR_SEP')) {
    define('DIR_SEP', DIRECTORY_SEPARATOR);
}
if(!defined('DIA_TEST_DIR')) define('DIA_TEST_DIR', dirname(__FILE__).DIR_SEP);
require_once( DIA_TEST_DIR.'config.php' );

require_once( DIA_DIR.'API.php' );

class TestDIA_API_HTTP_Request extends UnitTestCase {

	var $api;

    function TestDIA_API_HTTP_Request () {
        $this->UnitTestCase('DIA API Test');
    }

	function setUp() {
		$this->api =& DIA_API::create('HTTP_Request');
	}

	function tearDown() {
		unset($this->api);
	}

}

dia_test_run(__FILE__);
?>
