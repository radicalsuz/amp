<?php
require_once( 'AMP/Content/Image.inc.php' );
require_once( 'AMP/System/File/Image.php');

class ContentImage_Resize {

    var $_image_ref;
    var $_crop_ref;
    var $_content_image_controller;

    var $versions = array();
    var $_version_keys = array( AMP_IMAGE_CLASS_ORIGINAL, AMP_IMAGE_CLASS_OPTIMIZED, AMP_IMAGE_CLASS_THUMB );

    var $_allowed_mimetypes = array( 'image/gif', 'image/jpeg', 'image/png');
            
    var $_widths = array( AMP_IMAGE_CLASS_THUMB => AMP_IMAGE_WIDTH_THUMB );
    var $_errors=array();

    function ContentImage_Resize ($filename=null) {
        if (isset( $filename )) $this->setImageFile( $filename );
    }

    ###############################
    ###  Core Functions         ###
    ###############################

    function execute() {
        if ( !isset($this->_image_ref )) return false;
        $this->makeVersions();
        
        return (empty( $this->_errors ));
    }

    function makeVersions() {
        foreach( $this->_version_keys  as $version_class ) {
            $custom_method = 'makeVersion' . ucfirst( $version_class );
            if ( method_exists( $this, $custom_method )) {
                $this->$custom_method( );
                continue;
            }
            $this->makeVersion( $version_class );
        }
    }

    function makeVersionOriginal( ){
        $target_path = $this->_content_image_controller->getPath( AMP_IMAGE_CLASS_ORIGINAL );
		if ($target_path == $this->_image_ref->getPath()) return;
        return $this->makeVersion( AMP_IMAGE_CLASS_ORIGINAL );
    }

    function makeVersion( $version_class, $save = true  ){
        $target_path = $this->_content_image_controller->getPath( $version_class );
        $source = $this->_getVersionSource( $version_class );

        $new_height = $source->height * ( $this->_widths[ $version_class ] / $source->width );
        $new_resource = &$source->resize( $this->_widths[ $version_class ], $new_height );

        if ( !$save ) return $new_resource;

        if ( !$source->write_image_resource( $new_resource, $target_path )) {
            $this->addError( sprintf( AMP_TEXT_ERROR_FILE_WRITE_FAILED, $target_path ));
            return false;
        }
        $this->setFilePermission( $target_path );

    }

    function setFilePermission( $target_file ){
        chmod ($target_file, 0755);
    }
            

    ###############################
    ###  Public Access Methods  ###
    ###############################

    function setImageFile( $filename ) {

        if ( !file_exists( $filename )) return false; 

        $image_ref = &new AMP_System_File_Image( $filename );
        if ( array_search( $image_ref->get_mimetype( ), $this->_allowed_mimetypes ) === FALSE ) {
            $this->addError( AMP_TEXT_ERROR_IMAGE_NOT_ALLOWED );
            return false;

        }
        $this->_content_image_controller = &new Content_Image( $image_ref->getName( ));
        $this->_image_ref = &$image_ref;
        $this->_initCrop( );
        $this->_setWidths( );
        return true;
    }

    function _initCrop( ) {
        $crop_path = $this->_content_image_controller->getPath( AMP_IMAGE_CLASS_CROP );
        if ( !file_exists( $crop_path )) return false;
        $this->_crop_ref = &new AMP_System_File_Image( $crop_path );
    }

    ################################
    ###  Private Config Methods  ###
    ################################

    function _setWidths() {
        if (!isset($this->_image_ref )) return false;

        $this->_widths[ AMP_IMAGE_CLASS_ORIGINAL ] = $this->_image_ref->width;
        $this->_widths[ AMP_IMAGE_CLASS_OPTIMIZED ] = $this->_getOptimizedWidth();

        if ($this->_image_ref->width < $this->_widths[ AMP_IMAGE_CLASS_OPTIMIZED ])
                $this->_widths[ AMP_IMAGE_CLASS_OPTIMIZED ] = $this->_image_ref->width;

        $thumb_source = $this->_getVersionSource( AMP_IMAGE_CLASS_THUMB );
        if ($thumb_source->width < $this->_widths[ AMP_IMAGE_CLASS_THUMB ])
                $this->_widths[ AMP_IMAGE_CLASS_THUMB ] = $thumb_source->width;
    }

    function _getVersionSource( $version_class ){
        if ( $version_class != AMP_IMAGE_CLASS_THUMB && $version_class != AMP_IMAGE_CLASS_CROP ) {
            return $this->_image_ref;
        }

        if ( !isset( $this->_crop_ref )) return $this->_image_ref;
        return $this->_crop_ref;
    }

    function _getOptimizedWidth() {
        if ( $this->_image_ref->width > $this->_image_ref->height ) {
            return AMP_IMAGE_WIDTH_WIDE;
        }
        return AMP_IMAGE_WIDTH_TALL;
    }


    ################################
    ###  Error Methods ###
    ################################

    function addError( $text, $name = null ) {
        trigger_error( $text );
        if (isset($name )) return $this->_errors[ $name ] = $text;
        return $this->_errors[] = $text;
    }

    function getErrors() {
        return $this->_errors;
    }

}

?>
