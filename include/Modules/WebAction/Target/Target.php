<?php

require_once( 'AMP/System/Data/Item.inc.php');

class WebAction_Target extends AMPSystem_Data_Item {

    var $datatable = "webaction_targets";
    var $name_field = "Last_Name";

    function WebAction_Target ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getName( ){
        return $this->getData( 'First_Name') . ' ' . $this->getData( 'Last_Name');
    }

    function getPosition( ){
        return $this->getData( 'occupation');
    }

    function getRegion( ){
        return $this->getData( 'region');
    }

}

?>
