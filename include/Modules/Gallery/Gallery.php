<?php

require_once( 'AMP/System/Data/Item.inc.php');

class Gallery extends AMPSystem_Data_Item {
    var $datatable   = 'gallerytype';
    var $name_field  = 'galleryname';
    var $_class_name = 'Gallery';

    var $_display;

    function Gallery( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function &getDisplay( $displayType = 'Full') {
        if ( isset( $this->_display )) return $this->display;
        //require_once( 'Modules/Gallery/Display.inc.php');
        //$display_class = 'Gallery_Display';
        //$requested_class = $display_class . $displayType;
        // if ( class_exists( $requested_class )) $display_class = $requested_class;
        
        require_once( 'Modules/Gallery/Public/Display.php');
        $display_class = 'Gallery_Public_Display';
        
        $this->_display = &new $display_class ( $this );
        return $this->_display;
    }

    function display() {
        $display = &$this->getDisplay();
        return $display->execute();
    }

    function getTitle( ){
        return $this->getName( );
    }

    function getBlurb( ){
        return $this->getData( 'description');
    }

    function getItemDate( ){
        return $this->getData( 'date');
    }

    function getParent( ){
        return $this->getData( 'parent' );
    }

    function getImageFilename( $choose_rand = false ){
        $value = $this->getData( 'img');
        if ( !$choose_rand ) return $value;

        $all_images = &AMPContentLookup_GalleryImages::instance( $this->id );
        if ( empty( $all_images)) return false;
        $just_images = array_values( $all_images );
        return $just_images[ rand( 0, count( $all_images)-1) ];

    }
    function &getImageRef( $choose_rand = false ) {
        if (! ($img_path = $this->getImageFileName( $choose_rand ))) return false;
        require_once( 'AMP/Content/Image.inc.php');
        $image = &new Content_Image( $img_path );
        return $image;
    }

    function getURL( ){
        return AMP_Url_AddVars( AMP_CONTENT_URL_GALLERY, 'gal='.$this->id );
    }

    function isLive( ){
        return $this->getData( 'publish');
    }

    function getOrder( ){
        return $this->getData( 'listorder');
    }

    function reorder( $new_order_value ){
        if ( $new_order_value == $this->getOrder( )) return false;
        $this->mergeData( array( 'listorder' => $new_order_value ));
        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update');
        $this->notify( 'reorder');
        return $result;

    }

    function move( $parent_id = false ) {
        $move_action = false;
        if ( !( $parent_id && $parent_id != $this->getParent( ))) return false; 

        $this->setParent( $parent_id );
        if ( !( $result = $this->save( ))) return false;

        $this->notify( 'update' );
        $this->notify( 'move' );
        return $result;
    }

    function setParent( $parent_id ){
        return $this->mergeData( array( 'parent' => $parent_id ));
    }

    function get_url_edit( ) {
        if ( !( isset( $this->id ) && $this->id )) return false;
        return AMP_Url_AddVars( AMP_SYSTEM_URL_GALLERY, array( 'id=' . $this->id ) );
    }

    function getListItemLimit( ) {
        return $this->getData( 'list_item_limit');
    }

    function _sort_default( &$item_set ) {
        $this->sort( $item_set, 'listOrder');
    }

    function getListOrder() {
        $defined_order= $this->getOrder();
        if (!$defined_order) $defined_order = AMP_SORT_MAX;
        return $defined_order . ':' .  $this->getName();
    }
}

?>
