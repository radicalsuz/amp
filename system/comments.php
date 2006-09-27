<?php
if ( !defined( 'AMP_MODULE_ID_COMMENTS')) define( 'AMP_MODULE_ID_COMMENTS', 23 );
$modid = AMP_MODULE_ID_COMMENTS;

require_once( 'AMP/Content/Article/Comment/ComponentMap.inc.php');

$map = &new ComponentMap_Article_Comment( );
$controller = &$map->get_controller( );
print $controller->execute( );

?>
