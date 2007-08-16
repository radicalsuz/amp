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

    //var $_image_attr = array( 'width' => '16', 'height' => '16', 'border' => '0', 'align' => 'left' );
    var $_image_attr = array( 'border' => '0', 'align' => 'left' );
    var $_image_width = 64;

    var $_css_class_container_list = 'tagged_items';

    var $_pager_active = false;

    var $_item_displays_custom = array( );
    var $_source_criteria = array( 'live' => 1 );

    function AMP_Content_Tag_Item_Public_List( $source = false, $criteria = array( ) ) {
        $this->__construct( $source, $criteria );
    }

    function _renderItem( &$source ) {
        $custom_display = $this->_get_custom_render_function( $source->getItemCategory( )) ;
        if ( $custom_display ) {
            return $custom_display( $source, $this ) ;
        }
        
        $image = $source->getImageRef( );
        $icon = $this->_makeIcon( $image );
        $height = $image ?
                    $height = round( $image->getHeight( ) * ( $this->_image_width / $image->getWidth( ) ))
                    : 0;
        
        $name = $source->getItemName( );
        $url = $source->getURL( );
        $link_attr = array( 'class' => $this->_css_class_container_list_item );
        if ( $height ) {
            $link_attr['style'] = 'height: '.$height.'px;' ;
        }

        $item_output = $this->_renderer->link( $url, 
                                                $icon . $this->_renderer->space( 2 ) .  $name , 
                                                $link_attr
                                                );

        return $this->_renderSubheader( $source ) . $item_output;

    }

    function _renderSubheader( &$source, $depth = 0 ) {
        $item_header = $source->getItemCategory( );
        if ( !$item_header ) return false;

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
        $image_url = AMP_url_add_vars( AMP_CONTENT_URL_IMAGE, array( 'action=resize', 'class=thumb', 'filename=' . $image->getName( ), 'width=' . $this->_image_width ));
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
            $itemSource->sort( $item_set, 'itemName' );
            $source =  $source + $item_set;
        }

        
    }

    function _get_custom_render_function( $item_type ) {
        if ( isset( $this->_item_displays_custom[ $item_type ]))  {
            return $this->_item_displays_custom[ $item_type ];
        }

        //check constants for defined RENDER method
        $custom_display_constant  = 'AMP_RENDER_LIST_ITEM_' . strtoupper( str_replace( ' ', '_', $item_type ));
        if ( !defined( $custom_display_constant )) {
            $this->_item_displays_custom[ $item_type ] = false;
            return false;
        } 
        
        //make sure RENDER method is defined as a function
        $custom_display_function = constant( $custom_display_constant );
        if ( !function_exists( $custom_display_function )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, 'AMP', $custom_display_function ));
            $this->_item_displays_custom[ $item_type ] = false;
            return false;
        }

        $this->_item_displays_custom[ $item_type ] = $custom_display_function ;
        return $custom_display_function;

    }

}

?>
