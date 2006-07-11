<?php

//load sysvar table data
require_once('AMP/Registry.php');
require_once( 'AMP/System/Setup/Setup.php');
$SystemSetup = & new AMP_System_Setup( AMP_Registry::getDbcon( ));
$SystemSetup->execute( );

?>
