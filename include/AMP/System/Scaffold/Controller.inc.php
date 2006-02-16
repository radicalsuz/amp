<?php

require_once( '%4\$s%1\$s/ComponentMap.inc.php');
require_once( 'AMP/System/Page.inc.php');

$map = &new ComponentMap_%1\$s( );
$systemPage = &new AMPSystem_Page( $dbcon, $map );

$systemPage->execute( );
print $systemPage->output( );

?>
