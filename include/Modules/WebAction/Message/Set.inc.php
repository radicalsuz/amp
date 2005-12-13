<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'Modules/WebAction/Message/Message.php' );

class WebActionMessageSet extends AMPSystem_Data_Set {
    var $datatable = 'action_history';
    var $sort = array( 'date', 'subject');

    function WebActionMessageSet( &$dbcon, $owner_id = null ){
        if ( isset( $owner_id )) $this->addCriteriaSender( $owner_id );
        $this->init( $dbcon );
    }

    function addCriteriaSender( $owner_id ){
        $this->addCriteria( 'memberid ='.$owner_id);
    }
}

?>
