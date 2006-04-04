<?php

require_once( 'Modules/WebAction/Target/ComponentMap.inc.php');
require_once( 'AMP/System/Page.inc.php');

$map = &new ComponentMap_WebAction_Target( );
$systemPage = &new AMPSystem_Page( $dbcon, $map );
if (isset($_GET['action']) && $_GET['action'] == "list")  $systemPage->showList( true );

$systemPage->execute( );
print $systemPage->output( );

?>
