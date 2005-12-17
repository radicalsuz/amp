<?php

require_once( 'Modules/Gallery/Display.inc.php');

class GalleryImageSet_Display extends Gallery_Display {
    function GalleryImageSet_Display( &$image ){
        $this->_gallery = &new Gallery( $image->dbcon, $image->getGallery( ));
    }

    function _HTML_title( $name ) {
        if ( !$name ) $name = "Photo Gallery";
        return $this->_HTML_in_P( $name, array( 'class' => $this->_css_class_title ) );
    }

}
?>
