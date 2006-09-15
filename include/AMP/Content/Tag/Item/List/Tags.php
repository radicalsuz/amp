<?php
require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Tag/Item/Item.php');

class AMP_Content_Tag_Item_List_Tags extends AMPSystem_List {

    var $name = "Tags";
    var $col_headers = array(
        'Image'     =>  '_makeThumb',
        'Tag'       =>  'tagName',
        );

    var $_source_object = 'AMP_Content_Tag_Item';
    var $suppress = array( 'editcolumn' => true, 'addlink' => true, 'sortlinks' => true, 'messages' => true, 'header' => true );

    var $_observers_source = array( 'AMP_System_List_Observer');
    var $_thumb_attr = array( );
    var $_sort = 'tagname';

    function AMP_Content_Tag_Item_List_Tags ( &$dbcon, $criteria = array( ) ) {
        $this->init( $this->_init_source( $dbcon, $criteria ));
        //$this->_initThumbAttrs( );
    }

    function _makeThumb( &$source, $column_name ) {
        require_once( 'AMP/Content/Image.inc.php');
        $img = $source->getTagImageRef( );
        if ( !$img ) return false;
        return $this->inDiv( 
                    $this->_HTML_image( 
                        AMP_Url_AddVars( AMP_SYSTEM_URL_IMAGE_VIEW, array( 'file=' . $img->getName( ) , 'class=' . AMP_IMAGE_CLASS_THUMB, 'height=30', 'action=resize')) , 
                        $this->_thumb_attr 
                        ), 
                    array( 'class' => 'tag_listing_icon')
                    );
    }

    function _initThumbAttrs( ) {
        return false;
        $reg = &AMP_Registry::instance();
        if ($thumb_attr = $reg->getEntry( AMP_REGISTRY_CONTENT_IMAGE_THUMB_ATTRIBUTES )) {
            $this->_thumb_attr = array_merge( $this->_thumb_attr, $thumb_attr );
        } else {
            $this->_thumb_attr['width'] = AMP_IMAGE_WIDTH_THUMB;
        }
        $this->_thumb_attr['align'] = 'right';
    }

    function _HTML_header( ) {
        return '<div class="tag_listing">'
                . '<div class="system_heading">Current Tags</div>';
    }

    function _HTML_footer( ) {
        return '</div>';
    }

    function _HTML_listRow( $currentrow ){
        $list_html = false;
        foreach( $this->col_headers as $header => $col ) {
            //$list_html .= $this->inDiv( $currentrow[ $col ], array( 'class' => 'list_row'));
            $list_html .= $currentrow[ $col ];
        }
        return $this->inDiv( $list_html, array( 'class' => 'list_row'));
    }

    function _setSortTagname( &$source ) {
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

}

?>
