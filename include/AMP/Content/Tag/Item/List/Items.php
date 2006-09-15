<?php
require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Tag/Item/Item.php');

class AMP_Content_Tag_Item_List_Items extends AMP_System_List_Form {

    var $name = "Tagged Items";
    var $col_headers = array(
        'Options'       =>  '_renderControls',
        'Item'       =>  'itemName',
        );

    var $_source_object = 'AMP_Content_Tag_Item';
    var $_sort = 'itemtype';

    var $suppress = array( 'addlink' => true, 'sortlinks' => true, 'messages' => true, 'header' => true );

    var $_observers_source = array( 'AMP_System_List_Observer');
    var $_thumb_attr = array( );

    #var $_sort_default = 'itemsByType';

    function AMP_Content_Tag_Item_List_Items ( &$dbcon, $criteria = array( ) ) {
        $this->init( $this->_init_source( $dbcon, $criteria ));
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
                . '<div class="system_heading">Tagged Items</div>';
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

    function _renderControls( &$source ) {
        return $this->inDiv( 
                    $this->_renderEditLink( $source ) 
                  . $this->_renderPreviewLink( $source ),
                  array( 'class' => 'list_row_controls')
                  );

    }

    function _renderEditLink( &$source ) {
        $edit_url = $source->get_url_edit( );
        if ( !$edit_url ) return false;
        return $this->link( 
                        $edit_url,
                        $this->image( AMP_SYSTEM_ICON_EDIT, array( 'alt' => AMP_TEXT_EDIT, 'width' => '16', 'height' => '16', 'border' => '0')),
                        array( 'title' => AMP_TEXT_EDIT_ITEM, 'target' => $this->_getEditLinkTarget( ))
                    );
    }

    function _renderPreviewLink( &$source ) {
        $preview_url = $source->getURL( );
        if ( !$preview_url ) return false;
        return $this->link( 
                        $source->getURL( ),
                        $this->image( AMP_SYSTEM_ICON_PREVIEW, array( 'alt' => AMP_TEXT_PREVIEW_ITEM, 'width' => '16', 'height' => '16', 'border' => '0')),
                        array( 'title' => AMP_TEXT_PREVIEW_ITEM, 'target' => '_blank')
                    );
    }

    /*
    function _setSortItemsByType( &$source ) {
        $itemSource = &new $this->_source_object ( AMP_Registry::getDbcon( ));
        $itemSource->sort( $this->source, $this->_sort, $sort_direction );

        
    }
    */

}

?>
