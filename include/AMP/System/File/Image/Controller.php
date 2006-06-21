<?php

require_once( 'AMP/System/File/Controller.php');

class AMP_System_File_Image_Controller extends AMP_System_File_Controller {

    function AMP_System_File_Image_Controller( ){
        $name = 'AMP_System_File_Image_Form';
        $this->init( $name, 'POST', AMP_SYSTEM_URL_IMAGES );
    }

    function commit_crop( ){
        require_once( 'AMP/Content/Image/Cropper.inc.php');
        $file_name = ( AMP_LOCAL_PATH . '/' . AMP_CONTENT_URL_IMAGES . AMP_IMAGE_CLASS_ORIGINAL . '/' . $this->_model_id );
        $this->_model->setFile( $file_name );
        $crop_form = &new AMP_Content_Image_Crop_Form( $this->_model );
        $crop_action = $crop_form->submitted( );
        if ( !$crop_action ){
            $this->_display->add( $crop_form, 'crop');
            return true;
        }
        if ( $crop_action == 'cancel' ){
            $this->clear_actions( );
            return false;
        }
        $crop_form->Build( true  );
        $crop_sizes = $crop_form->getValues( );
        unset( $crop_sizes['submitCropAction']);
        AMP_varDump( $crop_sizes );
        $real_sizes = &$this->_resize_ratio( $crop_sizes, $crop_form->getDisplayRatio( ) );

        $target_image = &new Content_Image( $this->_model->getName( ) );
        $target_path  = $target_image->getPath( AMP_IMAGE_CLASS_CROP );
        $new_image = &$this->_model->crop( $real_sizes['start_x'], $real_sizes['start_y'], $real_sizes['width'], $real_sizes['height']);
        $this->_model->write_image_resource( $new_image, $target_path );
        
        $cropped_image = &new AMP_System_File_Image( $target_path );
        if ( !$cropped_image->width ){
            $this->error( 'Crop creation failed');
            return false;
        }

        $target_path = $target_image->getPath( AMP_IMAGE_CLASS_THUMB );

        $thumb_ratio = AMP_IMAGE_WIDTH_THUMB / $cropped_image->width;
        $thumb_sizes = $this->_resize_ratio( 
                            array( 'height' => $cropped_image->height,
                                    'width' => $cropped_image->width ), 
                            $thumb_ratio );
        $thumb_image = &$cropped_image->resize( $thumb_sizes['width'], $thumb_sizes['height'] );
        $cropped_image->write_image_resource( $thumb_image, $target_path );

        return true;

    }

    function _resize_ratio( $original_sizes, $ratio ){
        $result_sizes = array( );
        foreach( $original_sizes as $key => $size ){
            $result_sizes[$key] = ceil( $size * $ratio );
        }
        return $result_sizes;
    }

    function commit_default( ){
        if ( $this->get_action( ) == 'crop' ) return false;
        return PARENT::commit_default( );
    }


}

?>
