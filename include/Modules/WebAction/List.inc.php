<?php
require_once( 'AMP/System/List.inc.php');
require_once( 'Modules/WebAction/Set.inc.php');

class WebAction_List extends AMPSystem_List {
    var $name = 'Web Actions';
    var $col_headers = array( 'Action Name' => 'name', 'ID'=>'id' );
    var $extra_columns = array( 'report'=> 'action_center.php?action=report&id=' );

    function WebAction_List( &$dbcon ) {
        $source = &new WebActionSet( $dbcon );
        $this->init( $source );
    }

    function addCriteriaLive() {
        $this->source->addCriteriaLive(); 
    }

}
?>
