<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Nav/Layout/Layout.php');

class AMP_Content_Nav_Layout_List extends AMPSystem_List{
    var $name = "Nav_Layout";
    var $col_headers = array( 
        'Name' => 'name',
        'Anchor' => '_describeLayoutAnchor',
        'ID'    => 'id');
    var $editlink = AMP_SYSTEM_URL_NAV_LAYOUT;
    var $name_field = 'name';
    var $_source_object = 'AMP_Content_Nav_Layout';
    var $_observers_source = array( 'AMP_System_List_Observer');
    var $_actions = array( 'delete');
    var $_url_add = AMP_SYSTEM_URL_NAV_LAYOUT_ADD;

    function AMP_Content_Nav_Layout_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
    }

    function _describeLayoutAnchor( &$source, $fieldname ){
        if ( !( $layout_anchor = $source->getLayoutAnchor( ))) return false;
        return ucwords( $layout_anchor['description'] ) . ': ' . $layout_anchor['name'];
    }
}
?>
