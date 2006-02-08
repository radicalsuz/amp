<?php

require_once( 'AMP/System/Data/Item.inc.php');

class Gallery extends AMPSystem_Data_Item {
    var $datatable = 'gallerytype';
    var $name_field = 'galleryname';
    var $_display;

    function Gallery( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function &getDisplay( $displayType = 'Full') {
        if ( isset( $this->_display )) return $this->display;
        require_once( 'Modules/Gallery/Display.inc.php');
        $display_class = 'Gallery_Display';
        $requested_class = $display_class . $displayType;

        if ( class_exists( $requested_class )) $display_class = $requested_class;
        
        $this->_display = &new $display_class ( $this );
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

    function getImageFilename( $choose_rand = false ){
        $value = $this->getData( 'img');
        if ( !$choose_rand ) return $value;

        $all_images = &AMPContentLookup_GalleryImages::instance( $this->id );
        if ( empty( $all_images)) return false;
        $just_images = array_values( $all_images );
        return $just_images[ rand( 0, count( $all_images)-1) ];

    }
    function &getImageRef( $choose_rand = false ) {
        if (! ($img_path = $this->getImageFileName( $choose_rand ))) return false;
        require_once( 'AMP/Content/Image.inc.php');
        $image = &new Content_Image( $img_path );
        return $image;
    }

    function getURL( ){
        return AMP_Url_AddVars( AMP_CONTENT_URL_GALLERY, 'gal='.$this->id );
    }

    function isLive( ){
        return $this->getData( 'publish');
    }
}

?>
