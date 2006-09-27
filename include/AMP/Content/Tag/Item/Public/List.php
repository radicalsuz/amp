<?php

require_once( 'AMP/Display/List.php');
require_once( 'AMP/Content/Tag/Item/Item.php');

class AMP_Content_Tag_Item_Public_List extends AMP_Display_List {

    var $name = "Tagged Items";
    var $_source_object = 'AMP_Content_Tag_Item';

    var $_sort_default = 'itemsByType';
    var $_sort_sql_default = 'item_type';

    var $_suppress_header = true;
    var $_suppress_messages = true;

    var $_image_attr = array( 'width' => '16', 'height' => '16', 'border' => '0', 'align' => 'left' );

    var $_css_class_container_list = 'tagged_items';

    var $_pager_active = false;

    function AMP_Content_Tag_Item_Public_List( $source = false, $criteria = array( ) ) {
        $this->__construct( $source, $criteria );
    }

    function _renderItem( &$source ) {
        
        $icon = $this->_makeIcon( $source->getImageRef( ) );
        $name = $source->getItemName( );
        $url = $source->getURL( );
        $item_output = $this->_renderer->link( $url, $icon . $name, array( 'class' => $this->_css_class_container_list_item ));

        return $this->_renderSubheader( $source ) . $item_output;

    }

    function _renderSubheader( &$source, $depth = 0 ) {
        $item_header = $source->getItemCategory( );
        if ( isset( $this->_current_subheaders[$depth ])
            && ( $item_header == $this->_current_subheaders[$depth] )) {
            return false; 
        }
        $this->_current_subheaders[$depth] = $item_header;
        return $this->_renderer->inDiv( 
                    $item_header,
                    array( 'class' => $this->_css_class_container_list_subheader )
                );

    }

    function _makeIcon( $image ) {
        if ( !$image ) return false;
        trigger_error( 'image found ' );
        $image_url = $image->getURL( AMP_IMAGE_CLASS_THUMB );
        return $this->_renderer->image( $image_url, $this->_image_attr );
    }

    function _setSortItemsByType( &$source ) {
        $source_segments = array( );
        foreach( $source as $user_tag_id => $source_item ) {
            $source_type = $source_item->getItemCategory( );
            $source_segments[ $source_type ][ $user_tag_id ] = &$source[ $user_tag_id ];
        }
        $itemSource = &new $this->_source_object ( AMP_Registry::getDbcon( ));

        $source = false;
        $source = array( );
        foreach( $source_segments as $item_type => $item_set ) {
            $itemSource->sort( $item_set );
            $source =  $source + $item_set;
        }

        
    }

}

?>
