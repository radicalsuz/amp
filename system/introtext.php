<?php
require_once("AMP/System/IntroText/ComponentMap.inc.php");

$map = &new ComponentMap_IntroText();
$controller = &$map->get_controller( );

print $controller->execute( );
/*
require_once("AMP/System/Page.inc.php");

$map = &new ComponentMap_IntroText();
$page = &new AMPSystem_Page ($dbcon, $map);
#if (isset($_GET['action']) && $_GET['action'] == "list")  $page->showList( true );

// allow default to link to referring module
if (isset( $_GET[ 'tool_id' ]) && ($tool_id = $_GET[ 'tool_id' ])) {
    $page->addCallback( 'form', 'setDefaultValue', array( 'modid', $tool_id ));
}

$page->execute();

print $page->output( );
*/

?>

