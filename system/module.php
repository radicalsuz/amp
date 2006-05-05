<?php

require_once( 'AMP/System/Tool/ComponentMap.inc.php');
require_once( 'AMP/System/Page.inc.php');

$map = &new ComponentMap_Tool( );
$systemPage = &new AMPSystem_Page( $dbcon, $map );

$systemPage->execute( );
print $systemPage->output( );

?>
