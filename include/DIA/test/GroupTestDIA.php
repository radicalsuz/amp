<?php

if(!defined('DIR_SEP')) {
    define('DIR_SEP', DIRECTORY_SEPARATOR);
}

if(!defined('DIA_TEST_DIR')) define('DIA_TEST_DIR', dirname(__FILE__).DIR_SEP);
require_once(DIA_TEST_DIR.'config.php');

class GroupTest_DIA extends GroupTest {
	function GroupTest_DIA($name = 'DIA Test Suite') {
		$this->GroupTest($name);
		$this->addTestFile('TestDIA_API.php');
		$this->addTestFile('TestDIA_Object.php');
	}

}

dia_test_run( __FILE__ );
?>
