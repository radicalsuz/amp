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
        $cache_filename = $this->get_cached_version( 'resize');
        if ( $cache_filename === true ) {
            return;
        }

        extract( $this->_sizes );

        $resized_resource = &$this->_image_ref->resize( $width, $height );
        $result = $this->_image_ref->write_image_resource( $resized_resource, $cache_filename, false );
        if ( $result ) {
            return $this->_display( $cache_filename );
        }
    }

    function _display( $file_path ) {
        if ( !file_exists( $file_path )) return false;
        $fRef = fopen( $file_path, 'r');
        fpassthru( $fRef );
        fclose( $fRef );
        return true;

    }


    function commit_display( ){
        return $this->_display( $this->_image_ref->getPath( ));
    }

    function execute( ){
        $action_method = 'commit_' . $this->_action;
        $this->image_header( );
        return $this->$action_method( );
    }

    function get_cached_version( $action ) {
        require_once( 'AMP/System/Cache/File.php');
        $file_cache =  new AMP_System_Cache_File( );
        /*
        $size_desc = "";
        foreach( $this->_sizes as $key => $value ) {
            if ( !$value ) continue;
            $size_desc .= $key.'-'. round( $value ) . '_SZS_';
        }
        $key_hash = $action . '_' . $this->_image_ref->getName( ) . '__' . $size_desc ;
        */
        $cache_key = AMP_CACHE_TOKEN_IMAGE . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        if ( defined( 'AMP_SYSTEM_USER_ID') && AMP_SYSTEM_USER_ID ) {
            $cache_key = AMP_System_Cache::identify( $cache_key, AMP_SYSTEM_USER_ID );
        }
        $file_name = $file_cache->authorize( $cache_key );
        $file_path = $file_cache->path( $file_name );
        if ( file_exists( $file_path )) {
            if (  rand( 1, 10) === 2 ) {
                trigger_error( 'found cached ' . $file_name);
            }
        }
        
        if ( $this->_display( $file_path )) {
            return true;
        }

        return $file_path;
        
    }
}


?>
