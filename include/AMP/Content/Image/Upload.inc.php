<?php
define( 'AMP_TEXT_ERROR_IMAGE_NOT_ALLOWED', "Could not determine the type of image. JPG, GIF, and PNG format only"); 
define( 'AMP_TEXT_ERROR_FILE_EXISTS', "File already exists: " );
define( 'AMP_TEXT_ERROR_FILE_WRITE_FAILED', "Failed to write :" );

class Content_Image_Resize {

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

    function Content_Image_Resize ($filename=null) {
        if (isset( $filename )) $this->setImageFile( $filename );
    }

    function setImageFile( $filename ) {

        $imagetype = exif_imagetype( $filename );
        if (!isset($this->_allowed_image_type_extensions[ $imagetype ])) {
            $this->addError( AMP_TEXT_ERROR_IMAGE_NOT_ALLOWED );
            return false;
        } 

        $this->_file_extension = $this->_allowed_image_type_extensions[ $imagetype ];
        $this->setFileName( AMP_removeExtension( basename( $filename ) ) ); 
        $this->_setOriginal( $filename );

    }

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

    function setFileName( $name ) {
        $this->_file_name = $name;
    }
    function saveImagesAMP() {
        if ($this->saveImage ($this->original, 'original')) {
            if ($this->saveImage ($this->pic, 'pic')) {
                if ($this->saveImage ($this->thumb, 'thumb')) {
                    return true;
                }
            }
        }
        return false;
    }

    function saveVersion( $version=AMP_IMAGE_CLASS_ORIGINAL ) {
        $imgpath = $this->getPath( $version );

        if (file_exists($imgpath)) {
            $this->addError (AMP_TEXT_ERROR_FILE_EXISTS.$imgpath) ;
            return false;
        }

        $write_function= $this->_imagewrite_methods[$this->_file_extension];

        if (! $write_function( $this->versions[ $version ], $imgpath ) ) {
            $this->addError( AMP_TEXT_ERROR_FILE_WRITE_FAILED );
            return false;
        }

        chmod ($imgpath, 0755);
        return true;
    }

    function getPath( $version ) {
        return  AMP_LOCAL_PATH . AMP_IMAGE_PATH . $version . 
            DIRECTORY_SEPARATOR . $this->_file_name .".". $this->_file_extension;
    }

    function addError( $text, $name = null ) {
        if (isset($name )) return $this->_errors[ $name ] = $text;
        return $this->_errors[] = $text;
    }

    function _makeVersion( $new_width , $version=AMP_IMAGE_CLASS_ORIGINAL ) {

        $image= &$this->versions[ AMP_IMAGE_CLASS_ORIGINAL  ];

        $start_height = imagesy( $image );
        $start_width =  imagesx( $image );

        $aspect_ratio = $start_width / $new_width;
        $new_height= $start_height / $aspect_ratio;

        if ( $new_width && $new_height ) {
            return ( $this->versions[ $version ] = $this->_resize( $image, $start_width, $new_width, $start_height, $new_height ) );
        }
        
    }

    function makeVersions() {
        foreach( $this->_version_keys  as $version ) {
            if (isset( $this->versions[ $version ] )) continue;
            if (!isset( $this->_widths[ $version ] )) continue;
            $this->_makeVersion( $version, $this->_widths[ $version ] );
        }
    }
            

    function &_resize( &$image, $start_width, $new_width, $start_height, $new_height ) {

        if ((!function_exists('ImageCreateTrueColor') || $this->_file_extension == "gif") {

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

}

?>
