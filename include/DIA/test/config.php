<?php

define('DIA_API_DEBUG', true);

define('DIA_API_ORGCODE', '31uGrjZkOydl3Q/rY7JuT3ZAZREfue5AA85AA0ec5dY=');
define('DIA_API_ORGANIZATION_KEY', 962);
define('DIA_API_USERNAME', 'test');
define('DIA_API_PASSWORD', 'test');

define('DIA_TEST_GROUP_KEY', 26239);
define('DIA_TEST_GROUP2_KEY', 26516);
define('DIA_TEST_DATA_PREFIX', 'DIATest');

if(!defined('DIR_SEP')) {
    define('DIR_SEP', DIRECTORY_SEPARATOR);
}

if(!defined('DIA_DIR')) {
    define('DIA_DIR', '..' . DIR_SEP);
}

if(!defined('DIA_TEST_DIR')) define('DIA_TEST_DIR', dirname(__FILE__).DIR_SEP);

if(! defined('SIMPLE_TEST')) {
	define('SIMPLE_TEST', DIA_TEST_DIR.'simpletest/');
}

require_once(SIMPLE_TEST.'unit_tester.php');
require_once(SIMPLE_TEST.'reporter.php');

function dia_test_run( $filename, $reporter='HtmlReporter' ){
	require_once($filename);
    $obj_name = basename( $filename , '.php' ) ;
    if(! defined('DIA_TEST_RUNNER') && class_exists( $obj_name) ) {
        define('DIA_TEST_RUNNER', true);

        $test = &new $obj_name();
        $test->run(new $reporter());
    }
}
?>
