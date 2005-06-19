<?php

if(! defined('SIMPLE_TEST')) {
    define('SIMPLE_TEST', 'simpletest/');
}
require_once(SIMPLE_TEST.'unit_tester.php');
require_once(SIMPLE_TEST.'reporter.php');

#require_once('AMP/UserData.php');
require_once('unit_tests/mocks/utility.functions.inc.php');

require_once(SIMPLE_TEST.'mock_objects.php');
require_once('adodb/adodb.inc.php');
require_once('adodb/drivers/adodb-mysql.inc.php');
Mock::generate('ADODB_mysql');
Mock::generate('ADORecordSet_mysql');
require_once ( 'unit_tests/UnitRunner.inc.php' );

?>
