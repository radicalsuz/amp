<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'Modules/WebAction/Message/Set.inc.php');
require_once( 'Modules/WebAction/Lookups.inc.php');

class WebActionMessage_List extends AMPSystem_List {
    var $col_headers = array( 
            'Subject'   =>  'subject',
            'Sent'      =>  'date',
            'Action'    =>  'actionid',
            'ID'        =>  'id')

    function WebActionMessage_List( &$dbcon ){
        $source = &new WebActionMessageSet( $dbcon );
        $this->init( $source );
        $this->addLookup( 'actionid', WebAction_Lookup::instance('names' ))
    }

    function addCriteriaSender( $owner_id ){
        $this->source->addCriteriaSender( $owner_id );
    }
}
?>
