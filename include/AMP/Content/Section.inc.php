<?php

require_once ( 'AMP/System/Data/Item.inc.php' );

class Section extends AMPSystem_Data_Item {

    var $datatable = "articletype";
    var $name_field = "type";

    function Section( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

}
?>
