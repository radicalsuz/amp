<?php
require_once( 'AMP/System/Data/Item.inc.php');

class AMP_Content_Tag_Item extends AMPSystem_Data_Item {
    var $datatable = "tags_items";

    function AMP_Content_Tag_Item( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getTagName( ) {
        $tag_id = $this->getTag( );
        $names_lookup = AMPSystem_Lookup::instance( 'tags');
        if ( !isset( $names_lookup[ $tag_id ])) return false;
        return $names_lookup[ $tag_id ];
    }

    function getTagImage( ) {
        $tag_id = $this->getTag( );
        $images_lookup = AMPSystem_Lookup::instance( 'tagImages');
        if ( !isset( $images_lookup[ $tag_id ])) return false;
        return $images_lookup[ $tag_id ];
    }

    function &getTagImageRef( ) {
        $empty_value = false;
        if (! ($img_path = $this->getTagImage())) return $empty_value;
        require_once( 'AMP/Content/Image.inc.php');
        $image = &new Content_Image( $img_path );
        return $image;
    }

    function getTag( ) {
        return $this->getData( 'tag');
    }

    function makeCriteriaUid( $uid ) {
        return 'item_type = "form" AND item_id = ' . $uid;
    }

    function makeCriteriaUser( $uid ) {
        return 'user_id = ' . $uid;
    }

    function makeCriteriaItem( $item_id ) {
        return 'item_id = ' . $item_id;
    }

    function makeCriteriaItemtype( $item_type ) {
        return 'item_type = ' . $this->dbcon->qstr( $item_type );
    }

}

?>
