<?php

class AMP_Content_Image extends AMPSystem_Data_Item {
    var $datatable = 'images';
    var $_image;
    var $_exact_value_fields = array( 'name' );
    var $_class_name = 'AMP_Content_Image';

    function AMP_Content_Image( &$dbcon, $id = null ) {
        $this->__construct( $dbcon, $id );
    }

    function delete( ) {
        $gallery_image_ids = AMP_lookup( 'galleries_by_image', $this->getName( ));
        if( $gallery_image_ids ) {
            require_once( 'Modules/Gallery/Image.inc.php');
            $finder = new GalleryImage( AMP_Registry::getDbcon( ));
            $images = $finder->find( array( 'id' => array_keys( $gallery_image_ids )));
            foreach( $images as $gallery_image ) {
                $gallery_image->delete( );
            }
        }
        return parent::delete( );
    }

}


?>
