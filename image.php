<?php

require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/Content/Image/Effects/Controller.php');

if ( AMP_cached_image_request( )) {
    exit;
}

$controller = &new AMP_Content_Image_Effects_Controller( );
$controller->execute( );

?>
