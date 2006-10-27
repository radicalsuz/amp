<?php

require_once( 'AMP/Content/Image.inc.php' );
require_once( 'AMP/Content/Page/Urls.inc.php' );
require_once( 'AMP/System/File/Image.php' );

class AMP_Content_Image_Effects_Controller {
    var $_image_ref;

    var $_action_values = array( 
        'crop'  =>  array(  'start_x', 'start_y', 'end_x', 'end_y', 'height', 'width'),
        'resize' => array(  'height' , 'width' ));
    var $_default_action;
    var $_image_sizes;

    var $_cache;

    function AMP_Content_Image_Effects_Controller( ){
        $this->__construct( );
    }

    function __construct( ){
        $this->read_request( );
    }

    function read_request( ){
        if ( !( isset( $_REQUEST['filename'] ) && $image_file = $_REQUEST['filename'] )) return; 
        $image_class = ( isset( $_REQUEST['image_class'] ) && $_REQUEST['image_class'] ) ? $_REQUEST['image_class'] : AMP_IMAGE_CLASS_ORIGINAL;
        $image_ref_content = &new Content_Image( $image_file );
        if ( !( file_exists( $image_ref_content->getPath( $image_class )))) return; 
        $this->set_file( $image_ref_content->getPath( $image_class ));
        
        $action = ( isset( $_REQUEST['action']) && $_REQUEST['action']) ? $_REQUEST['action'] : $this->_default_action;

        //validate request
        if ( !( AMP_local_request( )
             || AMP_Authenticate( 'admin'))) {
            $action = $this->_default_action;
        }

        $display = &$this->get_display( );
        $display->set_action( $action );
        $this->read_request_sizes( $action );
    }

    function &get_display( ){
        if ( isset( $this->_display )) return $this->_display;
        require_once( 'AMP/Content/Image/Effects/Display.php');
        $this->_display = &new AMP_Content_Image_Effects_Display( );
        $this->_display->set_image( $this->_image_ref );
        return $this->_display;
    }

    function read_request_sizes( $action ) {

        if ( $action == $this->_default_action ) return;
        $tainted_values = array_combine_key( $this->_action_values[ $action ], $_REQUEST );
        $clear_values = array( );
        foreach( $this->_action_values[ $action ] as $key ) {
            $clear_values[$key] = ( isset( $tainted_values[$key])  && intval( $tainted_values[ $key ]))  ? abs( intval( $tainted_values[ $key ]  ))  : 0;
        }

        if ( $action == 'resize' ){
            if ( !$clear_values['width'] && $clear_values['height']) $clear_values['width'] = $this->_image_ref->width * ( $clear_values['height'] / $this->_image_ref->height );
            if ( !$clear_values['height'] && $clear_values['width']) $clear_values['height'] = $this->_image_ref->height * ( $clear_values['width'] / $this->_image_ref->width );
        }

        if ( isset( $clear_values['height']) && !$clear_values['height'])  $clear_values['height'] = $this->_image_ref->height;
        if ( isset( $clear_values['width'])  && !$clear_values['width'])   $clear_values['width']  = $this->_image_ref->width;
        
        if ( $action == 'crop' ) {
            if ( !$clear_values['end_x'] ) $clear_values['end_x'] = $clear_values['start_x'] + $clear_values['width'];
            if ( !$clear_values['end_y'] ) $clear_values['end_y'] = $clear_values['start_y'] + $clear_values['height'];
            if ( $clear_values['end_x']> $this->_image_ref->width || $clear_values['end_x']< $clear_values['start_x']) $clear_values['end_x']= $this->_image_ref->width;
            if ( $clear_values['end_y']> $this->_image_ref->height|| $clear_values['end_y']< $clear_values['start_y']) $clear_values['end_y']= $this->_image_ref->height;

            if ( $clear_values['start_x']>= $this->_image_ref->width || $clear_values['start_y']>= $this->_image_ref->height ) {
                $this->_display->set_action( $this->_default_action );
            }


        }

        $this->_image_sizes = $clear_values;
        $this->_display->set_sizes( $clear_values );

    }

    function set_file( $filename ){
        $this->_image_ref = &new AMP_System_File_Image( $filename );

    }

    function execute( ){
        if ( !isset( $this->_image_ref )) return;
        $display = &$this->get_display( );
        $display->execute( );
    }


    function hasImageSizes( $action ){
        if ( !isset( $this->_action_values[$action])) return false;
        foreach( $this->_action_values[$action] as $desired_value ){
            if ( !isset( $this->_image_sizes[$desired_value])) return false;
        }
        return true;
    }

    function get_sizes( ) {
        return $this->_image_sizes;
    }
}

?>
