<?php

$modid = 62;

require_once( "AMP/BaseDB.php" );
require_once( "AMP/BaseTemplate.php" );
require_once( 'Modules/WebAction/Controller.inc.php');

$actionMaker = &WebAction_Controller::instance( 'WebAction');
$actionMaker->execute();

require_once( "AMP/BaseFooter.php" ); 

?>
