<?php

require_once( 'AMP/System/Data/Set.inc.php');

class GallerySet extends AMPSystem_Data_Set {
    var $datatable = 'gallerytype';
    var $sort = array( 'galleryname' );
    var $_display;

    function GallerySet( &$dbcon ){
        $this->init( $dbcon );
    }
    function &getDisplay() {
        if ( isset( $this->_display )) return $this->display;
        require_once( 'Modules/Gallery/SetDisplay.inc.php');
        $this->_display = &new GallerySet_Display( $this );
        return $this->_display;
    }

    function display() {
        $display = &$this->getDisplay();
        return $display->execute();
    }

}
?>
