<?php

require_once( 'AMP/Display/List.php' );
require_once( 'AMP/Content/Tag/Item/Item.php' );

class AMP_Content_Item_Tag_Public_List extends AMP_Display_List {
    var $name = 'ItemTags';
    var $_source_object = 'AMP_Content_Tag_Item';
    var $_suppress_messages = true;
    var $_sort_default = 'tagName';

    var $_css_class_container_list = 'list_item_tags';

    function AMP_Content_Item_Tag_Public_List( $source = false, $criteria = array( )) {
        $this->__construct( $source, $criteria );
    }

    function _renderItem( &$source ) {
        //default, should be overridden
        $url = $source->getTagURL( );
        return $this->_renderer->link( $url, $source->getTagName( ), array( 'class' => 'title' ))
                . $this->_renderer->newline( );
    }

    function _renderHeader( ) {
        return $this->_renderer->inDiv( ucfirst( AMP_pluralize( AMP_TEXT_TAG )), array( 'class' => 'system_heading list_header'));
    }
}

?>
