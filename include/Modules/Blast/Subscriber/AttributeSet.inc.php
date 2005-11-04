<?php
require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'Modules/Blast/ComponentMap.inc.php');

class BlastSubscriber_AttributeSet extends AMPSystem_Data_Set {
    var $datatable = PHPLIST_TABLE_USER_ATTRIBUTE;
    
    function BlastSubscriber_AttributeSet( $dbcon ) {
        $this->init( $dbcon );
    }
}

?>
