<?php

class AMP_Content_Image_Effects_Display {
    var $_image_ref;
    var $_sizes;
    var $_allowed_actions = array( 'crop', 'resize', 'display' );
    var $_action = 'display';

    function AMP_Content_Image_Effects_Display( ){

    }
    function set_image( &$image_ref ){
        $this->_image_ref = &$image_ref;
    }

    function image_header( ){
        header( "Content-Type: " . $this->_image_ref->get_mimetype( ));
    }

    function set_action( $action ){
        if ( array_search( $action, $this->_allowed_actions ) === FALSE ) return false;
        $this->_action = $action;
    }

    function set_sizes( $sizes ){
        $this->_sizes = $sizes;
    }

    function commit_crop( ){
        extract( $this->_sizes );

        $cropped_resource = &$this->_image_ref->crop( $start_x, $start_y, $end_x, $end_y );
        $this->_image_ref->write_image_resource( $cropped_resource, null, true );
    }

    function commit_resize( ){
        extract( $this->_sizes );

        $resized_resource = &$this->_image_ref->resize( $width, $height );
        $this->_image_ref->write_image_resource( $resized_resource, null, true );
    }


    function commit_display( ){
        $fRef = fopen( $this->_image_ref->getPath( ), 'r');
        fpassthru( $fRef );
    }

    function execute( ){
        $action_method = 'commit_' . $this->_action;
        $this->image_header( );
        return $this->$action_method( );
    }
}


?>
