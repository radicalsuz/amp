<?php

require_once( 'AMP/Content/Article/ComponentMap.inc.php');

$map = &new ComponentMap_Article();
$controller = &$map->get_controller( );
print $controller->execute( );

/*
require_once( 'AMP/System/Page.inc.php');

$map = &new ComponentMap_Article();
$page = &new AMPSystem_Page ($dbcon, $map);
if (isset($_GET['action']) && $_GET['action'] == "list")  $page->showList( true );

$page->execute();
print $page->output( );
*/
?>
