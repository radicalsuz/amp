<?php
require_once ( 'AMP/System/Data/Item.inc.php');
require_once ( 'Modules/Blast/ComponentMap.inc.php');

class BlastSubscription extends AMPSystem_Data_Item {
    var $datatable = PHPLIST_TABLE_LIST_USER;
    var $id_field = array( 'userid', 'listid' );

    function BlastSubscription( &$dbcon ) {
        $this->init( $dbcon );
    }

}
?>
