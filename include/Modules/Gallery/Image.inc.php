<?php

require_once( 'AMP/System/Data/Item.inc.php');

class GalleryImage extends AMPSystem_Data_Item {
    var $datatable = "gallery";
    var $name_field  = 'img';
    var $_class_name = 'GalleryImage';
    var $_sort_auto = false;
    var $_exact_value_fields = array( 'img' );


    function GalleryImage( &$dbcon, $id = null ){
        $this->init( $dbcon, $id );
    }
    
    function _adjustSetData( $data ) {
        $this->legacyFieldname( $data, 'photoby', 'author' );
    }

    function &getDisplay() {
        if ( isset( $this->_display )) return $this->display;
        require_once( 'Modules/Gallery/Display.inc.php');
        $this->_display = &new Gallery_Display( $this );
        return $this->_display;
    }

    function display() {
        $display = &$this->getDisplay();
        return $display->execute();
    }

    function getImageFileName( ){
        return $this->getData( 'img');
    }

    function setImageFileName( $filename ){
        return $this->mergeData( array( 'img' => $filename ));
    }

    function &getImageRef() {
        if (! ($img_path = $this->getImageFileName())) return false;
        require_once( 'AMP/Content/Image.inc.php');
        $image = &new Content_Image( );
        $image->setData( $this->getImageData( ));
        return $image;
    }

    function getImageFile( ) {
        if (! ($img_name = $this->getImageFileName())) return false;
        require_once( 'AMP/System/File/Image.php');
        $image = new AMP_System_File_Image( AMP_image_path( $img_name ));
        $image->set_display_metadata( $this->getImageData( ));
        return $image;
    }
    function getImageData() {
        return array(   
            'filename'  =>  $this->getImageFileName( ),
            'author'    =>  $this->getSource( ),
            'date'      =>  $this->getAssignedDate( ),
            'caption'   =>  $this->getCaption( )
            );
    }

    function getSection( ){
        return $this->getData( 'section');
    }

    function getCaption( ){
        return $this->getData( 'caption');
    }

    function getSource( ){
        return $this->getData( 'photoby');
    }

    function getGallery( ){
        return $this->getData( 'galleryid');
    }

    function setGallery( $gallery_id ){
        return $this->mergeData( array( 'galleryid' => $gallery_id ));
    }
    function isLive( ){
        return $this->getData( 'publish');
    }

    function makeCriteriaDisplayable( ) {
        return $this->makeCriteriaLive( true );
    }

    function getItemDate( ){
        if ( !$this->isPublicDate( )) return false;
        $date = $this->getAssignedDate( );
        if ( $date ) return $date; 
        $image= $this->getImageRef( );
        return $image->getData( 'date');
    }

    function getAssignedDate( ) {
        return $this->getData( 'date');
    }

    function isPublicDate( ) {
        return $this->getData( 'usedate' );
    }

    function setItemDate( $date_value ){
        return $this->mergeData( array( 'date' => $date_value ));
    }
    function getGalleryName( ){
        if ( !$id = $this->getGallery( )) return false;
        //$galleries = AMPContent_Lookup::instance( 'galleries');
        $galleries = AMP_lookup( 'galleries');
        if ( !$galleries ||  !isset( $galleries[$id])) return false;
        return $galleries[$id];
    }

    function getOrder( ){
        return $this->getData( 'listorder' );
    }

    function reorder( $new_order_value ){
        if ( $new_order_value == $this->getOrder( )) return false;
        $this->mergeData( array( 'listorder' => $new_order_value ));
        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update');
        $this->notify( 'reorder');
        return $result;

    }

    function move( $gallery_id = false ) {
        $move_action = false;
        if ( !( $gallery_id && $gallery_id != $this->getGallery( ))) return false; 

        $this->setGallery( $gallery_id );
        if ( !( $result = $this->save( ))) return false;

        $this->notify( 'update' );
        $this->notify( 'move'   );
        return $result;
    }

    function makeCriteriaGallery( $gallery_id ) {
        return 'galleryid ='.$gallery_id;
    }

    function makeCriteriaStatus( $status_value ) {
        if ( $status_value === '' ) return false;
        return 'publish = ' . $status_value;
    }

    function getListOrder( ){
        $value = $this->getData( 'listorder' );
        if ( !$value ) $value = AMP_SORT_MAX;; 
        $value .= '_' . $this->getName( ) ;
        return $this->getGalleryName( ) . '_' . $value;
    }

    function get_url_edit( ) {
        if ( !$this->id ) return false;
        return AMP_url_add_vars( AMP_SYSTEM_URL_GALLERY_IMAGE, array( 'id='.$this->id)) ;
    }

}
?>
