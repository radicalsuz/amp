<?php
//
//  $Id$
//


ini_set('include_path', realpath(dirname(__FILE__).'/../../../').':'.
                        realpath(dirname(__FILE__).'/../../../installed').':'.
						ini_get('include_path'));
ini_set('error_reporting',E_ALL);						

require_once 'PHPUnit.php';
require_once 'PHPUnit/GUI/HTML.php';

require_once 'SQL/Query.php';
require_once 'SQL/Query/Join.php';

require_once 'UnitTest.php';

//
//  run the test suite
//

require_once 'PHPUnit/GUI/SetupDecorator.php';
$gui = new PHPUnit_GUI_SetupDecorator(new PHPUnit_GUI_HTML());
$gui->getSuitesFromDir(dirname(__FILE__),'.*\.php',array('UnitTest.php','index.php'));
$gui->show();

//print_r($errors);

?>
