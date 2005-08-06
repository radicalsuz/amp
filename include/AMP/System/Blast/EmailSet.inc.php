<?php

require_once( 'AMP/System/Data/Set.inc.php' );

class BlastEmailSet extends AMPSystem_Data_Set {

    var $datatable;

    function BlastEmailSet( &$dbcon ) {
        $this->init( $dbcon );
    }

    function doStraightSQL( $sql ) {
        if ($this->source = $this->dbcon->CacheExecute($sql)) {
            return true;
        }

        trigger_error ( get_class( $this ) . ' failed to get data : ' . $this->dbcon->ErrorMsg() . "\n statement: " . $sql );
        return false;
    }
}

?>
