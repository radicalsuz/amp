<?php

require_once( 'Modules/Calendar/Type/ComponentMap.inc.php');
require_once( 'AMP/System/Page.inc.php');

$map = &new ComponentMap_Calendar_Type( );
$systemPage = &new AMPSystem_Page( $dbcon, $map );

$systemPage->execute( );
print $systemPage->output( );

?>
