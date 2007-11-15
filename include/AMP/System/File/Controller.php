<?php

require_once( 'AMP/System/Component/Controller.php');

class AMP_System_File_Controller extends AMP_System_Component_Controller_Standard {

    var $_file_name_affected;

    function AMP_System_File_Controller( ){
        $this->init( );
    }

    function commit_crop( ){
        //do nothing
    }

    function commit_edit( ){
        return $this->commit_list( );
    }

    function commit_save( ) {
        //just-in-time Build call is a performance optimization, sorry for the repetitive code
        $this->_init_form( );

        if ( !$this->_form->validate( )){
            $this->_display->add( $this->_form, 'form' );
            return false;
        }

        $values = $this->get_form_data( );
        $this->_commit_save_actions( $values );

        $this->message( sprintf( AMP_TEXT_DATA_SAVE_SUCCESS, $this->_file_name_affected ),
                        $this->_unique_action_key( ), 
                        $this->_model->get_url_edit( ) );

        $this->display_default( );
        return true;
    }

    function _commit_save_actions( $values ){
        if ( isset( $values['file_upload'] )) $this->_file_name_affected = $values['file_upload'];
    }

    function _save_galleryInfo( $data ){
        if ( !( isset( $data['galleryid']) && !empty($data['galleryid']))) return false;
        require_once( 'Modules/Gallery/Image.inc.php');
        $galleries = is_array( $data['galleryid']) ? array_keys( $data['galleryid'] ) : array( $data['galleryid']);
        foreach( $galleries as $gallery_id ) {
            $gallery_image = &new GalleryImage( AMP_Registry::getDbcon( ) );
            $data['galleryid'] = $gallery_id;
            $gallery_image->setData( $data );
            $gallery_image->save( );
        }
    }

    function commit_megaupload( ){
        $renderer = AMP_get_renderer( );
        $buffer = new AMP_Content_Buffer( ) ;
        $buffer->add( 
              $renderer->newline( 2 )
            . $renderer->link( "javascript:showPopup( '". AMP_url_add_vars( "file_uploader.php", array( 'doctype=img' )). "' );",
                                AMP_TEXT_UPLOAD . $renderer->space( ) . AMP_TEXT_FILE )
            . $renderer->newline( 2 )
            );
        $this->_display->add( $buffer );
        return true;
    }

}

?>
