<?php

require_once( 'AMP/System/Page/Content.inc.php' );
require_once( 'AMP/Content/Map/ComponentMap.inc.php' );

$map = &new ComponentMap_ContentMap();
$page = &new AMPSystem_Page_Content( $dbcon, $map );
$page->addComponent('search');
$page->default_action = "menu";
$page->addComponentHeader( 'menu', 'Site Map' );
$page->addComponent('classlinks');

$page->execute();

$page->orderComponents(  array('search', 'list', 'menu', 'classlinks') );

print $page->output();

?>
