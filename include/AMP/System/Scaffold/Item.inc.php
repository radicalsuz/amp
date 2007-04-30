<?php

require_once( 'AMP/System/Data/Item.inc.php');

class %1\$s extends AMPSystem_Data_Item {

    var $datatable = "%2\$s";
    var $name_field = "%3\$s";

    function %1\$s ( &$dbcon, $id = null ) {
        $this->__construct( $dbcon, $id );
    }
}

?>
