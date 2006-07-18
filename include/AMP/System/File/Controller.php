<?php

require_once( 'AMP/System/Component/Controller.php');

class AMP_System_File_Controller extends AMP_System_Component_Controller_Standard {

    var $_file_name_uploaded;

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

        $this->message( sprintf( AMP_TEXT_DATA_SAVE_SUCCESS, $this->_file_name_uploaded ));

        $this->display_default( );
        return true;
    }

    function _commit_save_actions( $values ){
        if ( isset( $values['file_upload'] )) $this->_file_name_uploaded = $values['file_upload'];
    }

    function _save_galleryInfo( $data ){
        if ( !( isset( $data['galleryid']) && $data['galleryid'])) return false;
        require_once( 'Modules/Gallery/Image.inc.php');
        $gallery_image = &new GalleryImage( AMP_Registry::getDbcon( ) );
        $gallery_image->setData( $data );
        return $gallery_image->save( );
    }

}

?>
