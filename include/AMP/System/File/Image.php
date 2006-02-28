<?php
require_once( 'AMP/System/File/File.php');

class AMP_System_File_Image extends AMP_System_File {

    var $_write_methods = array(
            'image/gif' => 'imagegif', 
            'image/jpeg' => 'imagejpeg', 
            'image/png' => 'imagepng' );

    var $_create_methods = array(
            'image/gif' => 'imagecreate', 
            'image/jpeg'=> 'imagecreatetruecolor', 
            'image/png' => 'imagecreatetruecolor' );

    var $_read_methods = array(
            'image/gif' => 'imagecreatefromgif', 
            'image/jpeg'=> 'imagecreatefromjpeg', 
            'image/png' => 'imagecreatefrompng' );

    var $_copy_methods = array( 
            'image/gif' => 'imagecopyresized', 
            'image/jpeg'=> 'imagecopyresampled', 
            'image/png' => 'imagecopyresampled' );
    
    
    var $_mimetype;
    var $height;
    var $width;

    function AMP_System_File_Image( $file_path = null ){
        if ( isset( $file_path )) $this->setFile( $file_path );
    }

    function setFile( $file_path ){
        PARENT::setFile( $file_path );
        $this->set_mimetype( );
        $this->set_size ();
    }

    function set_size( ) {
        $size_data = getimagesize( $this->getPath( ) ); 
        $this->width = $size_data[0];
        $this->height = $size_data[1];
    }

    function set_mimetype( $value = null ){
        if ( isset( $mimetype_value ) ) return $this->_mimetype = $value;
        if ( !function_exists( 'mime_content_type')) return false;
        if ( !( $mime_filetype = mime_content_type( $this->getPath() )) ) return false;
        $this->_mimetype = $mime_filetype ;
    }

    function get_mimetype( ){
        return $this->_mimetype;
    }

    function &get_image_resource( ){
        if( !($read_method = $this->_get_action_method( 'read') )) return false;
        return $read_method( $this->getPath( ));
    }

    function &create_image_resource( $width, $height ) {
        if( !($create_method = $this->_get_action_method( 'create') )) return false ;
        return $create_method( $width, $height ) ;
    }

    function _get_action_method( $action ) {
        $method_set = '_' . $action .'_methods' ;
        $methods = $this->$method_set;
        if ( !isset( $methods[ $this->get_mimetype( )])) {
            trigger_error( sprintf( AMP_TEXT_ERROR_IMAGE_LIBRARY_NOT_FOUND, $action, $this->getName( ), $this->get_mimetype( )) );
            return false ;
        }
        return $methods[ $this->get_mimetype( )] ;
    }

    function write_image_resource( &$image_resource, $path = null, $direct = false ) {
        if ( !( isset( $path ) || $this->getName( ) || $direct )) {
            trigger_error( 'No path specified for writing image.');
            return false;
        }
        if ( !isset( $path )) $path = $this->getPath( );

        if( !($write_method = $this->_get_action_method( 'write') )) return false;

        if ( $direct ) return $write_method( $image_resource );
        return $write_method( $image_resource, $path );
    }

    function &resize( $new_width, $new_height, $start_width = null, $start_height = null ) {
        $copy_method = $this->_get_action_method( 'copy');
        $start = &$this->get_image_resource( );
        if ( !( $start && $copy_method ) ) return false;

        if ( !isset( $start_height )) $start_height = $this->height;
        if ( !isset( $start_width ))  $start_width  = $this->width ;

        $result = &$this->create_image_resource( $new_width, $new_height );

        $copy_method( $result, $start, 0,0,0,0, $new_width, $new_height, $start_width, $start_height );
        return $result;
    }

    function &crop( $start_x, $start_y, $end_x, $end_y ){
        $create_method = $this->_get_action_method( 'create');
        $copy_method = $this->_get_action_method( 'copy' );
        $source = &$this->get_image_resource( );
        if ( !( $source && $create_method )) return false;

        $initial_width = $end_x - $start_x; 
        $initial_height = $end_y - $start_y; 

        $result = &$create_method( $initial_width, $initial_height );
        $copy_method($result, $source, 0, 0, $start_x, $start_y, $initial_width, $initial_height, $initial_width, $initial_height);
        return $result;
    }

    function get_height( ){
        return $this->_height;
    }

    function get_width( ){
        return $this->_width;
    }
}

?>
