<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Link/Type/Type.php');
require_once( 'AMP/System/Data/Tree.php');

class Link_Type_List extends AMP_System_List_Form {

    var $name = "Link_Type";
    var $col_headers = array( 
        'Name' => 'name',
        'Status' => 'publish',
        'Order' => 'order',
        'ID'    => 'id'
        );
    var $editlink = 'link_type.php';
    var $_url_add = 'link_type.php?action=add';

    var $name_field = 'name';
    var $_source_object = 'Link_Type';

    var $_actions       = array( 'publish', 'unpublish', 'delete', 'reorder' );
    var $_action_args   = array( 'reorder' => array( 'order') );
    var $_actions_global= array( 'reorder' );

    var $_observers_source = array( 'AMP_System_List_Observer' );

    function Link_Type_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
        $this->_tree = &new AMP_System_Data_Tree( new Link_Type( AMP_Registry::getDbcon( )));
    }

    function _after_init( ){
        $this->addTranslation( 'order', '_makeInput');
        $this->addTranslation( 'name', '_formattedName');
    }

    function renderReorder( &$toolbar ){
        $action = 'reorder';
        return '&nbsp;&nbsp;&#124;&nbsp;&nbsp;' . $toolbar->renderDefault( $action );

    }
    function _formattedName( $value, $column_name, $data ) {
        if ( !isset( $this->_sort )) {
            return str_replace( strip_tags( $data[$this->name_field]), $data[$this->name_field], $this->_tree->render_option( $data['id'] ));
        }
        return $value;
    }

    function _setSortTree( &$source, $sort_direction = false ) {
        $lookup = &new AMPContentLookup_LinkTypeMap( );
        $lookup_data = $lookup->dataset;
        $order = array_keys( $lookup_data );
        $source = array_combine_key( $order, $source );
    }

    function _after_sort( &$source ) {
        $this->_setSortTree( $source );
    }
}
?>
