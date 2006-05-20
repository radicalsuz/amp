<?php

require_once( 'AMP/System/Data/Item.inc.php');

class FAQ extends AMPSystem_Data_Item {

    var $datatable = "faq";
    var $name_field = "question";

    function FAQ ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getType( ){
        return $this->getData( 'typeid' );
    }
}

?>
