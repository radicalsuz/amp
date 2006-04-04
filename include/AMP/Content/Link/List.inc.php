<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Link/Link.php');
require_once( 'AMP/System/List/Observer.inc.php');

class AMP_Content_Link_List extends AMP_System_List_Form {
    var $name = "Link";
    var $col_headers = array( 
        'Link' => 'name',
        'URL' => 'url',
        'Order' => 'order',
        'Type' => 'LinkTypeName',
        'Status' => 'publish',
        'ID'    => 'id');
    var $editlink = 'links.php';
    var $name_field = 'linkname';
    var $_source_object = 'AMP_Content_Link';
    var $_observers_source = 'AMP_System_List_Observer';
    var $_actions = array( 'publish', 'unpublish', 'delete', 'reorder');
    var $_action_args = array( 'reorder' => array( 'order'));
    var $_actions_global = array( 'reorder');

    function AMP_Content_Link_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
        $this->addTranslation( 'order', '_makeInput');
    }

    function renderReorder( &$toolbar ){
        $action = 'reorder';
        return '&nbsp;&nbsp;&#124;&nbsp;&nbsp;' . $toolbar->renderDefault( $action );

    }
}
?>
