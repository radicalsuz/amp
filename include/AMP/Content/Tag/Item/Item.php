<?php
require_once( 'AMP/System/Data/Item.inc.php');

class AMP_Content_Tag_Item extends AMPSystem_Data_Item {
    var $datatable = "tags_items";
    var $_class_name = 'AMP_Content_Tag_Item';

    var $_item_type;

    var $_tagged_item;

    var $_tagged_class_form = 'AMP_System_User_Profile';
    var $_tagged_class_article = 'Article';

    var $_tagged_path_form = 'AMP/System/User/Profile/Profile.php';
    var $_tagged_path_article = 'AMP/Content/Article.inc.php';

    function AMP_Content_Tag_Item( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function _adjustSetData( $data ) {
        if ( !isset( $this->_tagged_item ) 
           || ( $this->_tagged_item->id != $this->getItemId( ) )) {
            $this->_init_tagged_item( );
        }
    }

    function getTagName( ) {
        $tag_id = $this->getTag( );
        $names_lookup = AMPSystem_Lookup::instance( 'tags' );
        if ( !isset( $names_lookup[ $tag_id ])) return false;
        return $names_lookup[ $tag_id ];
    }

    function getTagImage( ) {
        $tag_id = $this->getTag( );
        $images_lookup = AMPSystem_Lookup::instance( 'tagImages' );
        if ( !isset( $images_lookup[ $tag_id ])) return false;
        return $images_lookup[ $tag_id ];
    }

    function getTagURL( ) {
        $tag_id = $this->getTag( );
        if ( !$tag_id ) return false;
        return AMP_Url_AddVars( AMP_CONTENT_URL_TAG, array( 'id='.$tag_id ));

    }

    function getItemDescription( ) {
        $item_type = $this->getItemtype( );
        $name_value = false;
        if ( $item_type == AMP_TEXT_SYSTEM_ITEM_TYPE_FORM ) {
            $form_id = $this->_tagged_item->getModin( );
            $form_names = AMPSystem_Lookup::instance( 'forms');
            if ( isset( $form_names[ $form_id ])) {
                $name_value = ': ' . $form_names[ $form_id ] ;
            }
        }
        return AMP_pluralize( ucfirst( $item_type )) . $name_value;
    }

    function getItemCategory( ) {
        $item_type = $this->getItemtype( );
        $name_value = false;
        if ( $item_type == AMP_TEXT_SYSTEM_ITEM_TYPE_FORM ) {
            $form_id = $this->_tagged_item->getModin( );
            $form_names = AMPSystem_Lookup::instance( 'formsPublic');
            if ( isset( $form_names[ $form_id ])) {
                return $form_names[ $form_id ] ;
            }
        }
        return AMP_pluralize( ucfirst( $item_type ) );

    }

    function &getTagImageRef( ) {
        $empty_value = false;
        if (! ($img_path = $this->getTagImage())) return $empty_value;
        require_once( 'AMP/Content/Image.inc.php' );
        $image = &new Content_Image( $img_path );
        return $image;
    }

    function getTag( ) {
        return $this->getData( 'tag_id');
    }

    function getItemtype( ) {
        return $this->getData( 'item_type');
    }

    function getItemId( ) {
        return $this->getData( 'item_id');
    }

    function getItemName( ) {
        return $this->_tagged_item->getName( );
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

    function makeCriteriaTag( $tag_id ) {
        return 'tag_id = ' . $tag_id;
    }

    function makeCriteriaTagname( $tagname ) {
        $tag_names = AMPSystem_Lookup::instance( 'tags' );
        if ( !$tag_names ) return 'FALSE';

        $tag_ids = array_keys( $tag_names, $tagname );
        if ( !$tag_ids ) return 'FALSE';

        return $this->makeCriteriaTag( current( $tag_ids ));
    }

    /*
    function _sort_default( &$item_set ){
        $names_lookup = &AMPSystem_Lookup::instance('tags' );
        $order = array_keys( $names_lookup );
        $translate_set = array( );
        foreach( $item_set as $item_id => $item ) {
            $tag_id = $item->getTag( );
            if ( !$tag_id ) continue;
            $translate_set[ $tag_id ] = $item_id;
        }

        $ordered_set = array_combine_key( $order, $translate_set);
        $item_set = array_combine_key( $ordered_set, $item_set );
    }
    */

    function _init_tagged_item( ) {
        if ( !$this->hasData( )) return false;
        $item_type = $this->getItemtype( );
        if ( !$item_type ) return false;

        $item_type_path = '_tagged_path_' . $item_type;
        if ( !isset( $this->$item_type_path ) && $this->$item_type_path) return false; 
        include_once( $this->$item_type_path );

        $item_type_class_var = '_tagged_class_' . $item_type;
        if ( !isset( $this->$item_type_class_var ) && $this->$item_type_class_var ) return false; 
        $item_type_class = $this->$item_type_class_var;
        $this->_tagged_item = & new $item_type_class( AMP_Registry::getDbcon( ), $this->getItemId( ) );
        if ( !$this->_tagged_item->hasData( )) {
            $this->delete( );
        }
    }

    function getName( ) {
        if ( !$this->hasData( )) return false;
        $this->_tagged_item->getName( );
    }

    function getURL( ) {
        if ( !method_exists( $this->_tagged_item, 'getURL')) return false;
        return $this->_tagged_item->getURL( );
    }

    function get_url_edit( ) {
        if ( !method_exists( $this->_tagged_item, 'get_url_edit')) return false;
        return $this->_tagged_item->get_url_edit( );
    }

    function getBlurb( ) {
        return $this->_tagged_item->getBlurb( );
    }

    function getImageRef( ) {
        return $this->_tagged_item->getImageRef( );
    }

}

?>
