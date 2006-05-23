<?php

require_once( 'AMP/System/Data/Item.inc.php');

class Blast_Config extends AMPSystem_Data_Item {
    var $datatable = 'phplist_config';
    var $id_field = 'item';
    var $name_field = 'item';

    function Blast_Config( &$dbcon, $id = null ){
        $this->init( $dbcon, $id );
    }

    function getValue( ){
        return $this->getData( 'value');
    }

    function setValue( $value ){
        return $this->mergeData( array( 'value' => $value ));
        
    }
}


?>
