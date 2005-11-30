<?php

require_once( 'Modules/WebAction/ComponentMap.inc.php');
require_once( 'AMP/System/Page.inc.php');

$map = &new ComponentMap_WebAction( );
$actionPage = &new AMPSystem_Page( $dbcon, $map );
if (isset($_GET['action']) && $_GET['action'] == "list")  $actionPage->showList( true );

$actionPage->execute( );
print $actionPage->output( );

?>
