<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'Modules/WebAction/Target/Target.php');

class WebAction_Target_List extends AMPSystem_List {
    var $name = "WebAction_Target";
    var $col_headers = array( 
        'Name' => 'name',
        'ID'    => 'id',
        'Position' => 'position',
        'Region'    => 'region',
        'Status'    => 'publish');
    var $editlink = 'webaction_targets.php';
    var $_source_object = 'WebAction_Target';

    function WebAction_Target_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ));
    }
}
?>
