<?php

require_once( 'AMP/System/Data/Item.inc.php');

class GalleryImage extends AMPSystem_Data_Item {
    var $datatable = "gallery";

    function GalleryImage( &$dbcon, $id = null ){
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

    function getImageFileName( ){
        return $this->getData( 'img');
    }

    function &getImageRef() {
        if (! ($img_path = $this->getImageFileName())) return false;
        require_once( 'AMP/Content/Image.inc.php');
        $image = &new Content_Image( );
        $image->setData( $this->getImageData( ));
        return $image;
    }
    function getImageData() {
        return array(   'filename'  =>  $this->getImageFileName(),
                        'caption'   =>  $this->getCaption( ));
    }

    function getSection( ){
        return $this->getData( 'section');
    }

    function getCaption( ){
        return $this->getData( 'caption');
    }

    function getSource( ){
        return $this->getData( 'photoby');
    }

    function getGallery( ){
        return $this->getData( 'galleryid');
    }
    function isLive( ){
        return $this->getData( 'publish');
    }
    function getItemDate( ){
        return $this->getData( 'date');
    }
    function getGalleryName( ){
        if ( !$id = $this->getGallery( )) return false;
        $galleries = &AMPContent_Lookup::instance( 'galleries');
        if ( !isset( $galleries[$id])) return false;
        return $galleries[$id];
    }

}
?>
