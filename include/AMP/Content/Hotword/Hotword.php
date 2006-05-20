<?php

require_once( 'AMP/System/Data/Item.inc.php');

class Hotword extends AMPSystem_Data_Item {

    var $datatable = "hotwords";
    var $name_field = "word";

    function Hotword ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getURL( ){
        return $this->getData( 'url');
    }
}

?>
