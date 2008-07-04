<?php

$map_id =  isset( $_GET['map']) ? intval( $_GET['map'] ) : false;
if ( !$map_id ) {
    $map_id = isset( $_GET['id']) ? intval( $_GET['id'] ) : false;
}

$height = isset( $_GET['height'] ) ? intval( $_GET['height'] ) : false ;
$width = isset( $_GET['width']) ? intval( $_GET['width'] ) : false;

if ( !$map_id ) exit;

require_once( 'AMP/BaseDB.php');
require_once("AMP/Geo/Maps.php");

$map = new Maps( AMP_Registry::getDbcon( ), $map_id );
if ( $height ) {
    $map->P['map_height'] = $height;
}
if ( $width ) {
    $map->P['map_width'] = $width;
}

$result = $map->flash_map( );
print AMP_js_write( $result );

?>
