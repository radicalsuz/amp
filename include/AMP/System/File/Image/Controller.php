<?php

require_once( 'AMP/System/File/Controller.php');

class AMP_System_File_Image_Controller extends AMP_System_File_Controller {

    var $_form_crop;

    function AMP_System_File_Image_Controller( ){
        $name = 'AMP_System_File_Image_Form';
        $this->init( $name, 'POST', AMP_SYSTEM_URL_IMAGES );
    }

    function _init_model( &$model ){
        $result = PARENT::_init_model( $model );
        if( isset( $this->_model_id ) && $crop_form = &$this->_map->getComponent( 'crop', $model )){
            $this->_form_crop = &$crop_form;
            $action = $crop_form->submitted( );
            if ( $action && $action != 'cancel') {
                $this->request( 'crop' );
            }
        }
        return $result;
    }

    function commit_crop( ){
        if ( !isset( $this->_model_id )){
            $this->clear_actions( );
            return false;
        }

        $file_name = ( AMP_LOCAL_PATH . '/' . AMP_CONTENT_URL_IMAGES . AMP_IMAGE_CLASS_ORIGINAL . '/' . $this->_model_id );
        $this->_model->setFile( $file_name );

        $this->_form_crop->Build( true  );
        $crop_action = $this->_form_crop->submitted( );
        if ( !$crop_action ){
            $this->_display->add( $this->_form_crop, 'crop');
            return true;
        }


        $crop_values = $this->_form_crop->getValues( );
        /*
        if ( !isset( $crop_values['id']) && $crop_values['id'] ) {
            $this->clear_actions( );
            return false;
        }
        $image_filename = $crop_values['id'];
		$image_path  = AMP_LOCAL_PATH . '/img/' . AMP_IMAGE_CLASS_ORIGINAL . '/' . $image_filename ;
        $this->_model->setFile( $image_path );
        */
        unset( $crop_values['submitCropAction']);
        unset( $crop_values['id']);
        $real_sizes = &$this->_resize_ratio( $crop_values, $this->_form_crop->getDisplayRatio( ) );

        $target_image = &new Content_Image( $this->_model->getName( ) );
        $target_path  = $target_image->getPath( AMP_IMAGE_CLASS_CROP );
		AMP_mkDir( substr( $target_path, 0, strlen( $target_path ) - strlen( $this->_model->getName() - 1)));
        $new_image = &$this->_model->crop( $real_sizes['start_x'], $real_sizes['start_y'], $real_sizes['start_x'] + $real_sizes['width'], $real_sizes['start_y'] + $real_sizes['height']);
        if ( !$new_image ) return $this->_commit_crop_failure( );

        $this->_model->write_image_resource( $new_image, $target_path );
        
        $cropped_image = &new AMP_System_File_Image( $target_path );
        if ( !$cropped_image->width ) return $this->_commit_crop_failure( );
        

        $target_path = $target_image->getPath( AMP_IMAGE_CLASS_THUMB );

        $thumb_ratio = AMP_IMAGE_WIDTH_THUMB / $cropped_image->width;
        $thumb_sizes = $this->_resize_ratio( 
                            array( 'height' => $cropped_image->height,
                                    'width' => $cropped_image->width ), 
                            $thumb_ratio );
        $thumb_image = &$cropped_image->resize( $thumb_sizes['width'], $thumb_sizes['height'] );
        if ( !$thumb_image ) return $this->_commit_crop_failure( AMP_TEXT_THUMBNAIL );
        $cropped_image->write_image_resource( $thumb_image, $target_path );
        $renderer = &new AMPDisplay_HTML( );
		$this->message( 
                $renderer->image( $target_image->getURL( AMP_IMAGE_CLASS_CROP ), array('border'=>1 )) 
                . $renderer->newline( 2 )
                . sprintf( AMP_TEXT_DATA_SAVE_SUCCESS, $cropped_image->getName() . $renderer->space() . AMP_TEXT_CROP )
            );
		$this->_display->add( $this->_map->getComponent('list'));

        return true;

    }

    function _commit_crop_failure( $attempted_item = AMP_TEXT_CROP ){
        $this->clear_actions( );
        $this->error( sprintf( AMP_TEXT_ERROR_CREATE_FAILED, $this->_model->getName( ), $attempted_item ));
        return false;
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

    function _commit_save_actions( $values ){
        if ( !isset( $values['image'])) return false;
        $image_name = $values['image'];
        $values['img'] = $image_name;
        $this->_save_galleryInfo( $values );

        require_once( 'AMP/Content/Image/Display.inc.php');
        $uploaded_image = &new ContentImage_Display_allVersions( $image_name );
        $this->_display->add( $uploaded_image );
        $this->_file_name_uploaded = $image_name;
    }


}

?>
