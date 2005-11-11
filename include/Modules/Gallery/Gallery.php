<?php

require_once( 'AMP/System/Data/Item.inc.php');

class Gallery extends AMPSystem_Data_Item {
    var $datatable = 'gallerytype';
    var $name_field = 'galleryname';
    var $_display;

    function Gallery( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function &getDisplay() {
        if ( isset( $this->_display )) return $this->display;
        require_once( 'Modules/Gallery/Display.inc.php');
        $this->_display = &new Gallery_Display( $this );
        return $this->_display;
    }

    function display() {
        $display = &$this->getDisplay();
        return $display->execute();
    }

    function getTitle( ){
        return $this->getName( );
    }

    function getBlurb( ){
        return $this->getData( 'description');
    }

    function getItemDate( ){
        return $this->getData( 'date');
    }

    function getImageFilename( ){
        return $this->getData( 'img');
    }
    function &getImageRef() {
        if (! ($img_path = $this->getImageFileName())) return false;
        require_once( 'AMP/Content/Image.inc.php');
        $image = &new Content_Image( $img_path );
        return $image;
    }
}

?>
