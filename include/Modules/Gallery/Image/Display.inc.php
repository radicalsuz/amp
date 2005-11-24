<?php

require_once( 'Modules/Gallery/Display.inc.php');

class GalleryImageSet_Display extends Gallery_Display {
    var $_pager_limit=1;
    var $_list_image_class = AMP_IMAGE_CLASS_OPTIMIZED;

    function GalleryImage_Display( &$image ){
        $this->_galleryimage = &$image;
    }

    function execute( ){

    }

    function _HTML_title( $name ) {
        if ( !$name ) $name = "Photo Gallery";
        return $this->_HTML_in_P( $name, array( 'class' => $this->_css_class_title ) );
    }
}
?>
