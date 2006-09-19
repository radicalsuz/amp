<?php
require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Tag/Tag.php');

class AMP_Content_Tag_List extends AMP_System_List_Form {

    var $col_headers = array(
        'ID'        =>  'id',
        'Tag'     =>  'name',
        'Image'   =>  '_makeThumb'
        );
    var $_source_object = 'AMP_Content_Tag';
    var $name_field = 'name';

    var $editlink = AMP_SYSTEM_URL_TAG;
    var $_url_add = AMP_SYSTEM_URL_TAG_INPUT;

    var $_observers_source = array( 'AMP_System_List_Observer');
    var $_actions = array( 'delete' );
    var $formname = "AMP_Tag_List";
    var $_thumb_attr;

    function AMP_Content_Tag_List( &$dbcon, $criteria = array( ) ) {
        $this->init( $this->_init_source( $dbcon, $criteria ));
        $this->_initThumbAttrs( );
    }

    function _makeThumb( &$source, $column_name ) {
        require_once( 'AMP/Content/Image.inc.php');
        $img = $source->getImageRef( );
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
