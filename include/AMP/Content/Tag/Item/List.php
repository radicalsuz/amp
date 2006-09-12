<?php
require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Tag/Item/Item.php');

class AMP_Content_Tag_Item_List extends AMPSystem_List {

    var $col_headers = array(
        'ID'        =>  'id',
        'Tag'     =>  'tagName',
        'Image'   =>  '_makeThumb'
        );
    var $_source_object = 'AMP_Content_Tag_Item';
    var $suppress = array( 'editlink' => true, 'addlink' => true, 'sortlinks' => true, 'messages' => true );

    var $_observers_source = array( 'AMP_System_List_Observer');

    function AMP_Content_Tag_Item_List( &$dbcon, $criteria = array( )) {
        $this->init( $this->_init_source( $dbcon, $criteria ));
        $this->_initThumbAttrs( );
    }

    function _makeThumb( &$source, $column_name ) {
        require_once( 'AMP/Content/Image.inc.php');
        $img = $source->getTagImageRef( );
        if ( !$img ) return false;
        return $this->_HTML_link(   $img->getURL( AMP_IMAGE_CLASS_ORIGINAL ), 
                                    $this->_HTML_image($img->getURL( AMP_IMAGE_CLASS_THUMB ), $this->_thumb_attr ), 
                                    array( 'target' => 'blank' )
                                );
    }

    function _initThumbAttrs( ) {
        $reg = &AMP_Registry::instance();
        if ($thumb_attr = $reg->getEntry( AMP_REGISTRY_CONTENT_IMAGE_THUMB_ATTRIBUTES )) {
            $this->_thumb_attr = array_merge( $this->_thumb_attr, $thumb_attr );
        } else {
            $this->_thumb_attr['width'] = AMP_IMAGE_WIDTH_THUMB;
        }
    }

}

?>
