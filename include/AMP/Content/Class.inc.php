<?php

require_once ('AMP/System/Data/Item.inc.php' );

class ContentClass extends AMPSystem_Data_Item {

    var $datatable = "class";
    var $name_field = "class";

    function ContentClass( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getSection() {
        return $this->getData( 'type' );
    }

}
?>
