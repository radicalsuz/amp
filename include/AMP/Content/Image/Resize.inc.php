<?php
define( 'AMP_TEXT_ERROR_IMAGE_NOT_ALLOWED', "Could not determine the type of image. JPG, GIF, and PNG format only"); 
define( 'AMP_TEXT_ERROR_FILE_EXISTS', "File already exists: " );
define( 'AMP_TEXT_ERROR_FILE_WRITE_FAILED', "Failed to write :" );
require_once( 'AMP/Content/Image.inc.php' );

class ContentImage_Resize {

    var $_file_extension;
    var $_file_name;

    var $versions = array();
    var $_version_keys = array( AMP_IMAGE_CLASS_ORIGINAL, AMP_IMAGE_CLASS_OPTIMIZED, AMP_IMAGE_CLASS_THUMB );
    var $imageRef;

    var $_allowed_image_type_extensions = array(
            IMAGETYPE_GIF => 'gif', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png' );
    var $_imagecreate_methods = array(
            'gif'=>'imagecreatefromgif', 'jpg'=>'imagecreatefromjpeg', 'png' => 'imagecreatefrompng' );
    var $_imagewrite_methods = array(
            'gif'=>'imagegif', 'jpg'=>'imagejpeg', 'png' => 'imagepng' );

    var $_widths = array( AMP_IMAGE_CLASS_THUMB => AMP_IMAGE_WIDTH_THUMB );
    var $_errors=array();

    function ContentImage_Resize ($filename=null) {
        if (isset( $filename )) $this->setImageFile( $filename );
    }

    ###############################
    ###  Core Functions   ###
    ###############################

    function execute() {
        if (!isset($this->versions[ AMP_IMAGE_CLASS_ORIGINAL ])) return false;
        $this->makeVersions();
        
        foreach( $this->_version_keys as $version ) {
            if (!isset($this->versions[ $version ])) continue;
            $this->saveVersion( $version );
        }

        return (empty( $this->_errors ));
    }

    function makeVersions() {
        foreach( $this->_version_keys  as $version ) {
            if (isset( $this->versions[ $version ] )) continue;
            if (!isset( $this->_widths[ $version ] )) continue;
            if ( $version == AMP_IMAGE_CLASS_ORIGINAL ) continue; 

            $this->_makeVersion( $version );
        }
    }
            

    function saveVersion( $version=AMP_IMAGE_CLASS_ORIGINAL ) {
        $imgpath = $this->getPath( $version );
        if ( $version == AMP_IMAGE_CLASS_ORIGINAL ) return true; 

        if (file_exists($imgpath)) {
            trigger_error(AMP_TEXT_ERROR_FILE_EXISTS.$imgpath) ;
        }

        $write_function= $this->_imagewrite_methods[$this->_file_extension];

        if (! $write_function( $this->versions[ $version ], $imgpath ) ) {
            $this->addError( AMP_TEXT_ERROR_FILE_WRITE_FAILED );
            return false;
        }

        chmod ($imgpath, 0755);
        return true;
    }


    ###############################
    ###  Public Access Methods  ###
    ###############################

    function setImageFile( $filename ) {

        
        if ( ! (($imagetype = exif_imagetype( $filename )) && 
                isset($this->_allowed_image_type_extensions[ $imagetype ]))) {
            $this->addError( AMP_TEXT_ERROR_IMAGE_NOT_ALLOWED );
            return false;
        } 

        $this->_file_extension = $this->_allowed_image_type_extensions[ $imagetype ];
        $this->setFileName( AMP_removeExtension( basename( $filename ) ) ); 
        return $this->_setOriginal( $filename );

    }

    function setFileName( $name ) {
        $this->_file_name = $name;
    }

    function getPath( $version ) {
        return  AMP_LOCAL_PATH . AMP_IMAGE_PATH . $version . 
            DIRECTORY_SEPARATOR . $this->_file_name .".". $this->_file_extension;
    }

    ################################
    ###  Private Config Methods  ###
    ################################

    function _setOriginal( $filename ) {
        if (!isset( $this->_imagecreate_methods[ $this->_file_extension ] )) return false;
        $create_method = $this->_imagecreate_methods[ $this->_file_extension ];

        if ($this->versions[ AMP_IMAGE_CLASS_ORIGINAL ] = &$create_method( $filename )) {
            $this->_setWidths();
            return true;
        }

        return false;
    }

    function _setWidths() {
        if (!isset($this->versions[ AMP_IMAGE_CLASS_ORIGINAL ])) return false;

        $start_height = imagesy( $this->versions[ AMP_IMAGE_CLASS_ORIGINAL ] );
        $start_width =  imagesx( $this->versions[ AMP_IMAGE_CLASS_ORIGINAL ] );
        $this->_widths[ AMP_IMAGE_CLASS_ORIGINAL ] = $start_width;
        $this->_widths[ AMP_IMAGE_CLASS_OPTIMIZED ] = $this->_getOptimizedWidth( $start_height, $start_width );
    }

    function _getOptimizedWidth( $start_height, $start_width ) {
        if ( $start_width > $start_height ) {
            return AMP_IMAGE_WIDTH_WIDE;
        }
        return AMP_IMAGE_WIDTH_TALL;
    }


    ################################
    ###  private Image Resize methods ###
    ################################


    function _makeVersion( $version=AMP_IMAGE_CLASS_ORIGINAL ) {

        $image= &$this->versions[ AMP_IMAGE_CLASS_ORIGINAL  ];
        $new_width = $this->_widths[ $version ];

        $start_height = imagesy( $image );
        $start_width =  imagesx( $image );

        $aspect_ratio = $start_width / $new_width;
        $new_height= $start_height / $aspect_ratio;

        if ( $new_width && $new_height ) {
            return ( $this->versions[ $version ] = &$this->_resize( $image, $start_width, $new_width, $start_height, $new_height ) );
        }
        
    }


    function &_resize( &$image, $start_width, $new_width, $start_height, $new_height ) {

        if ((!function_exists('ImageCreateTrueColor')) || $this->_file_extension == "gif") {

            // if GD library 2.0 is not installed
            // or the file is a GIF

            $result_image = &ImageCreate($new_width,$new_height);
            ImageCopyResized($result_image, $image, 0,   0,   0,   0, $new_width, $new_height, $start_width, $start_height); 
            return $result_image;

        }

        $result_image = &ImageCreateTrueColor($new_width,$new_height);
        ImageCopyResampled($result_image, $image, 0,   0,   0,   0, $new_width, $new_height, $start_width, $start_height); 

        return $result_image;
    }


    ################################
    ###  Error Methods ###
    ################################

    function addError( $text, $name = null ) {
        if (isset($name )) return $this->_errors[ $name ] = $text;
        return $this->_errors[] = $text;
    }

    function getErrors() {
        return $this->_errors;
    }

}

?>
