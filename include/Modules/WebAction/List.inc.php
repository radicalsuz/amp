<?php
require_once( 'AMP/System/List/Form.inc.php');
require_once( 'Modules/WebAction/Set.inc.php');
require_once( 'AMP/System/List/Observer.inc.php');

class WebAction_List extends AMP_System_List_Form {
    var $name = 'Web Actions';
    var $col_headers = array( 'Action Name' => 'name', 'Status' => 'status', 'ID'=>'id' );
    var $extra_columns = array( 'report'=> 'action_center.php?action=report&id=' );
    var $_url_add = 'action_center.php?action=add';
    var $_source_object = 'WebAction';
    var $_observers_source = array( 'AMP_System_List_Observer');

    function WebAction_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
    }

    function addCriteriaLive() {
        $this->source->addCriteriaLive(); 
    }

}
?>
