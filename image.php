<?php

require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/Content/Image/Effects/Controller.php');

$controller = &new AMP_Content_Image_Effects_Controller( );
$controller->execute( );

/*
//Confirm that a file has been requested
if ( !( isset( $_GET['filename'] ) && $image_file = $_GET['filename'] )) {
    ampredirect( AMP_CONTENT_URL_INDEX ) ;
}


//Confirm that the file exists in the images folder with a valid path
$image_ref_content = &new Content_Image( $image_file );
$image_class = ( isset( $_GET['image_class'] ) && $_GET['image_class'] ) ? $_GET['image_class'] : AMP_IMAGE_CLASS_ORIGINAL;
if ( !( file_exists( $image_ref_content->getPath( $image_class )))) {
    ampredirect( AMP_CONTENT_URL_INDEX ) ;
}

//Confirm that the file is a valid image resource
$image_ref = &new AMP_System_File_Image( $image_ref_content->getPath( $image_class ));
if ( !( $image_resource = &$image_ref->get_image_resource( ))){
    ampredirect( AMP_CONTENT_URL_INDEX ) ;
}

//Send image header
if ( !defined( 'AMP_CONTENT_PAGE_REDIRECT')) header( "Content-Type: " . $image_ref->get_mimetype( ));

//Confirm
$allowed_admin_actions = array( 'crop', 'resize', 'display' );
$action = ( isset( $_GET['action']) && $_GET['action']) ? $_GET['action'] : 'display';
if ( ! AMP_Authenticate( 'admin' )) $action = 'display';

$next_action = 'doImageAction' . ucfirst( $action );

$image_action_array = array();
$image_action_array[] = &$image_ref;
$image_action_array[] = &$image_resource;

call_user_func_array( $next_action, array( $image_ref, $image_resource ));

function doImageActionDisplay( $image_ref, &$image_resource ){
    $image_ref->write_image_resource( $image_resource, null, true );
}

function doImageActionResize( $image_ref, &$image_resource ){
    $height = ( isset( $_REQUEST['height']) && $_REQUEST['height']) ? $_REQUEST['height'] : $image_ref->height;
    $width  = ( isset( $_REQUEST['width'])  && $_REQUEST['width'])  ? $_REQUEST['width']  : $image_ref->width;
    $resized_resource = &$image_ref->resize( $width, $height );
    $image_ref->write_image_resource( $resized_resource, null, true );
}


function doImageActionCrop ( $image_ref, &$image_resource ){
    $end_y =  ( isset( $_REQUEST['end_y'])  && intval( $_REQUEST['end_y']))  ? abs( intval( $_REQUEST['end_y']  ))  : false;
    $end_x  = ( isset( $_REQUEST['end_x'])  && intval( $_REQUEST['end_x']))  ? abs( intval( $_REQUEST['end_x']  ))  : false;
    $height = ( isset( $_REQUEST['height']) && intval( $_REQUEST['height'])) ? abs( intval( $_REQUEST['height'] ))  : $image_ref->height;
    $width  = ( isset( $_REQUEST['width'])  && intval( $_REQUEST['width']))  ? abs( intval( $_REQUEST['width']  ))  : $image_ref->width;

    $start_y =  ( isset( $_REQUEST['start_y'])  && intval( $_REQUEST['start_y'])) ? abs( intval( $_REQUEST['start_y'] )): 0;
    $start_x =  ( isset( $_REQUEST['start_x'])  && intval( $_REQUEST['start_x'])) ? abs( intval( $_REQUEST['start_x'] )) : 0;
    if ( !$end_x ) $end_x = $start_x + $width;
    if ( !$end_y ) $end_y = $start_y + $height;
    if ( $end_x > $image_ref->width || $end_x < $start_x ) $end_x = $image_ref->width;
    if ( $end_y > $image_ref->height || $end_y < $start_y) $end_y = $image_ref->height;
    if ( $start_x >= $image_ref->width || $start_y >= $image_ref->height ){
        return ;
    }

    $cropped_resource = &$image_ref->crop( $start_x, $start_y, $end_x, $end_y );
    $image_ref->write_image_resource( $cropped_resource, null, true );
}
*/

?>
