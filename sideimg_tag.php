<?php

$image_name = AMP_find_banner_image( );
if ( $image_name ) {
    require_once( 'AMP/Content/Image.inc.php');
    $renderer = AMP_get_renderer( );
    $imageRef = &new Content_Image( $image_name );
    $image_url = $imageRef->getURL( AMP_IMAGE_CLASS_ORIGINAL );
    print $renderer->image( $image_url );
}

?>
