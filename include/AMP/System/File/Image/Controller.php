<?php

require_once( 'AMP/System/File/Controller.php' );

class AMP_System_File_Image_Controller extends AMP_System_File_Controller {

    var $_form_crop;

    function AMP_System_File_Image_Controller( ){
        $name = 'AMP_System_File_Image_Form';
        $this->init( $name, 'POST', AMP_SYSTEM_URL_IMAGES );
    }

    function _init_model( &$model ){
        $result = parent::_init_model( $model );

        if( !isset( $this->_model_id )) return $result; 
        if( is_numeric( $this->_model_id )) {
            $this->_model->read( $this->_model_id );
        } else {
            $file_name = ( AMP_LOCAL_PATH . '/' . AMP_CONTENT_URL_IMAGES . AMP_IMAGE_CLASS_ORIGINAL . '/' . $this->_model_id );
            $this->_model->setFile( $file_name );

        }


        $crop_form = &$this->_map->getComponent( 'crop', $this->_model );
        if ( !$crop_form ) return $result;

        $this->_form_crop = &$crop_form;
        $action = $crop_form->submitted( );
        if ( $action && $action != 'cancel' ) {
            $this->request( 'crop' );
        }
        return $result;
    }

    function commit_add( ) {
        if( AMP_params( 'action') == 'review') {
            return parent::commit_add( );
        }

        $filename = AMP_params( 'file');
        $this->_model->setFile( AMP_image_path( $filename ));

        if( $this->_model->db_id( )) {
            ampredirect( AMP_url_update( $_SERVER['REQUEST_URI'], array( 'action' => 'edit', 'file' => '', 'id' => $this->_model->db_id( ) )));
            return true;
        }
        return parent::commit_add( );
    }

    function display_default( ) {
        if ( ( $this->get_action( ) != 'save' ) || empty( $_FILES )) {
            return parent::display_default( );
        }
        //clear the REQUEST
        $_POST = array( );
        $this->_form = &$this->_map->getComponent( 'form' );
        $this->_form->initNoId( );
        $this->_init_form( false );
        $this->set_banner( 'add');
        $this->_display->add( $this->_form, 'default' );
        return true;
    }

    function commit_crop( ){
        if ( !isset( $this->_model_id )){
            $this->clear_actions( );
            return false;
        }

        $this->_form_crop->applyDefaults( );
        $this->_form_crop->Build( true  );
        $crop_action = $this->_form_crop->submitted( );
        if ( !$crop_action || ( $crop_action && !$this->_form_crop->validate( ))){
            $this->_form_crop->setValues( array( 'id' => $this->_model->getName( )));
            $this->_display->add( $this->_form_crop, 'crop');
            return true;
        }

        $crop_values = $this->_form_crop->getValues( );
        $crop_target = $crop_values['target'];

        unset( $crop_values['submitCropAction']);
        unset( $crop_values['target']);
        unset( $crop_values['id']);
        $real_sizes = &$this->_resize_ratio( $crop_values, $this->_form_crop->getDisplayRatio( ) );

        if ( $crop_target == 'thumb' ){
            $result = $this->_commit_crop_thumbnail( $real_sizes );
        } elseif ( $crop_target == 'all' ){
            $result = $this->_commit_crop_original( $real_sizes );
        }

		$this->_display->add( $this->_map->getComponent('list'));
        
        return $result;

    }

    function _commit_crop_original( $real_sizes ){
        $target_path  = AMP_image_path( $this->_model->getName( ), AMP_IMAGE_CLASS_ORIGINAL );
        $new_image = &$this->_model->crop( $real_sizes['start_x'], $real_sizes['start_y'], $real_sizes['start_x'] + $real_sizes['width'], $real_sizes['start_y'] + $real_sizes['height']);
        if ( !$new_image ) return $this->_commit_crop_failure( );

        $this->_model->write_image_resource( $new_image, $target_path );
        $cropped_image = &new AMP_System_File_Image( $target_path );
        if ( !$cropped_image->width ) return $this->_commit_crop_failure( );

        require_once( 'AMP/Content/Image/Resize.inc.php' );
        $resizer = &new ContentImage_Resize( $target_path );
        if ( !$resizer->execute( )) {
            return $this->_commit_crop_failure( );
        }
        $renderer = &new AMPDisplay_HTML( );
		$this->message( 
                $renderer->image( AMP_image_url( $this->_model->getName( ), AMP_IMAGE_CLASS_ORIGINAL ), array('border'=>1 )) 
                . $renderer->newline( 2 )
                . sprintf( AMP_TEXT_DATA_SAVE_SUCCESS, $cropped_image->getName()),
                $this->_unique_action_key( ),
                $this->_model->get_url_edit( )
            );
        return true;

    }

    function _commit_crop_thumbnail ( $real_sizes ){
        $target_path  = AMP_image_path( $this->_model->getName( ), AMP_IMAGE_CLASS_CROP );
		#AMP_mkdir( substr( $target_path, 0, strlen( $target_path ) - strlen( $this->_model->getName() - 1)));
		AMP_mkdir( dirname( $target_path ));
        $new_image = &$this->_model->crop( $real_sizes['start_x'], $real_sizes['start_y'], $real_sizes['start_x'] + $real_sizes['width'], $real_sizes['start_y'] + $real_sizes['height']);
        if ( !$new_image ) return $this->_commit_crop_failure( );

        $this->_model->write_image_resource( $new_image, $target_path );
        
        $cropped_image = &new AMP_System_File_Image( $target_path );
        if ( !$cropped_image->width ) return $this->_commit_crop_failure( );
        

        $target_path = AMP_image_path( $this->_model->getName( ), AMP_IMAGE_CLASS_THUMB );

        $thumb_ratio =  AMP_IMAGE_WIDTH_THUMB / $cropped_image->width;
        $thumb_sizes = $this->_resize_ratio( 
                            array( 'height' => $cropped_image->height,
                                   'width'  => $cropped_image->width  ), 
                            $thumb_ratio );

        $thumb_image = &$cropped_image->resize( $thumb_sizes['width'], $thumb_sizes['height'] );
        if ( !$thumb_image ) return $this->_commit_crop_failure( AMP_TEXT_THUMBNAIL );
        $cropped_image->write_image_resource( $thumb_image, $target_path );
        $renderer = &new AMPDisplay_HTML( );
		$this->message( 
                $renderer->image( AMP_image_url( $this->_model->getName( ), AMP_IMAGE_CLASS_CROP ), array('border'=>1 )) 
                . $renderer->newline( 2 )
                . sprintf( AMP_TEXT_DATA_SAVE_SUCCESS, $cropped_image->getName() . $renderer->space() . AMP_TEXT_CROP ), 
                $this->_unique_action_key( ),
                $this->_model->get_url_edit( )
            );
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

    function _commit_default( ){
        if ( $this->get_action( ) == 'crop' ) return false;
        return parent::_commit_default( );
    }

    function _save_image_db( $data ) {
        $db_data = $data;
        if ( !( isset( $data['id']) && $data['id'])) {
            //create new db record
            if( isset( $data['folder']) && $data['folder']) {
                $db_data['name']    = $data['folder'] . DIRECTORY_SEPARATOR . $data['image'];
                $db_data['folder']  = $data['folder'];
            } else {
                $db_data['name']    = $data['image'];

            }
            $db_data['publish'] = AMP_CONTENT_STATUS_LIVE;
            $db_data['created_at'] = date( "Y-m-d h:i:s" );
            $db_data['created_by'] = AMP_SYSTEM_USER_ID;
        } else {
            // update db record
            $db_data['updated_at'] = date( "Y-m-d h:i:s");
        }

        //read height and width from image file
        $this->_model->setFile( AMP_image_path( $this->_file_name_affected, AMP_IMAGE_CLASS_ORIGINAL ));
        $db_data['height'] = $this->_model->height;
        $db_data['width'] =  $this->_model->width;
        $this->_model->set_display_metadata( $db_data );
        AMP_lookup_clear_cached( 'db_images');

        require_once( 'AMP/Content/Image/Image.php');
        $image = new AMP_Content_Image( AMP_Registry::getDbcon( ));
        $image->setDefaults( );
        //db data has to be merged explicitly to include blank values
        $image->mergeData( $db_data );
        $image->mergeData( $this->_model->getData( ));
        return $image->save( );

    }

    function _commit_save_actions( $values ) {
        $db_images = AMP_lookup( 'db_images');
        if( isset( $this->_model_id ) && isset( $db_images[$this->_model_id])) {
            $image_name = $this->_file_name_affected = $db_images[$this->_model_id];
        } elseif ( !(isset( $values['image']) && $values['image'])) {
            return false;
        } else {
            $image_name = $this->_file_name_affected = $values['image'];
            if( isset( $values['folder']) && $values['folder']) {
                $image_name = $this->_file_name_affected = $values['folder'] . DIRECTORY_SEPARATOR . $values['image'];
                
            }
        }

        $values['img'] = $image_name;
        $this->_save_galleryInfo( $values );
        $this->_save_image_db( $values );
        if( empty( $_FILES )) return;

        require_once( 'AMP/Content/Image/Display.inc.php');
        $this->_model->setFile( AMP_image_path( $image_name , AMP_IMAGE_CLASS_ORIGINAL));
        $buffer = new AMP_Content_Buffer( );
        $buffer->add( $this->_model->display->render_proofsheet( $this->_model ));
        $this->_display->add( $buffer );
        $this->_file_name_affected = $image_name;
        $this->_update_image_cache_add( $image_name );
        ampredirect( AMP_url_update( AMP_SYSTEM_URL_IMAGES, array( 'action' => 'review', 'review_file' => $image_name )));
    }

    function commit_review( ) {
        if( !AMP_params( 'review_file') ) return $this->commit_add( );
        $image_name = AMP_params( 'review_file' );

        require_once( 'AMP/Content/Image/Display.inc.php');
        $this->_model->setFile( AMP_image_path( $image_name , AMP_IMAGE_CLASS_ORIGINAL));
        $buffer = new AMP_Content_Buffer( );
        $buffer->add( $this->_model->display->render_proofsheet( $this->_model ));
        $this->_display->add( $buffer );
        return $this->commit_add( );
    }

    function commit_edit( ) {
        //just-in-time Build call is a performance optimization, sorry for the repetitive code
        $this->_init_form( );

        if ( !$this->_model->read( $this->_model_id )) {
            return $this->commit_list( );
        }
        $data = $this->_model->getData( );
        $this->_form->setValues( $this->_model->getData( ));
        $this->_form->drop_uneditable_fields( );
		$this->_display->add( $this->_form, 'form' ); 
		return true;
    }


    
    function _update_image_cache_add( $image_name ){
        $imageRef = &new AMP_System_File_Image( AMP_image_path( $image_name, AMP_IMAGE_CLASS_ORIGINAL ));
        $image_cache_key = $imageRef->getCacheKeySearch( );
        $image_cache = &AMP_cache_get( $image_cache_key );

        if ( !$image_cache ) return;

        $image_cache[ $image_name ] = &$imageRef;
        AMP_cache_set( $image_cache_key, $image_cache );
    }

}

?>
