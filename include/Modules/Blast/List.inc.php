<?php

require_once( 'Modules/Blast/List.inc.php');

class BlastList extends AMPSystem_Data_Item {
    var $datatable = PHPLIST_TABLE_LIST;
    
    function BlastList( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }
}
?>
