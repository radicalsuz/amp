<?php
require_once("AMP/System/IntroText/ComponentMap.inc.php");

$map = &new ComponentMap_IntroText();

//require_once( 'AMP/System/Page.inc.php');
//$page = &new AMPSystem_Page( AMP_Registry::getDbcon( ), $map );
//$page->execute( );
//print $page->output( );

$controller = &$map->get_controller( );

print $controller->execute( );

?>

