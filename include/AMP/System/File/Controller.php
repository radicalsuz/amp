<?php

require_once( 'AMP/System/Component/Controller.php');

class AMP_System_File_Controller extends AMP_System_Component_Controller_Standard {

    function AMP_System_File_Controller( ){
        $this->init( );
    }

    function commit_crop( ){

    }

    function commit_edit( ){
        return $this->commit_list( );
    }

    function commit_save( ) {
        if ( !$this->_form->validate( )){
            $this->_display->add( $this->_form, 'form' );
            return false;
        }

        $values = $this->get_form_data( );
        $image_name = $values['image'];
        $values['img'] = $image_name;
        $this->_save_galleryInfo( $values );

        $this->message( sprintf( AMP_TEXT_DATA_SAVE_SUCCESS, $image_name));

        require_once( 'AMP/Content/Image/Display.inc.php');
        $uploaded_image = &new ContentImage_Display_allVersions( $image_name );
        $this->_display->add( $uploaded_image );
        $this->display_default( );
        return true;
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
