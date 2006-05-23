<?php
require_once( 'AMP/System/Data/Item.inc.php');

class Forum_Config extends AMPSystem_Data_Item {

    var $datatable = 'punbb_config';
    var $id_field = 'conf_name';
    var $name_field = 'conf_name';

    function Forum_Config( &$dbcon, $id = null ){
        $this->init( $dbcon, $id );
    }

    function getValue( ){
        return $this->getData( 'conf_value');
    }

    function setValue( $value ){
        return $this->mergeData( array( 'conf_value' => $value ));
    }

}

?>
