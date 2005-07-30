<?php

require_once( 'AMP/System/Data/Item.inc.php' );
require_once( 'AMP/System/Data/Set.inc.php' );

class UserData_Action extends AMPSystem_Data_Item {

    var $service = 'default';

    var $datatable = "userdata_actions";

    function UserData_Action( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function setData( $data ) {
        $data['service'] = $this->service;
        return PARENT::setData( $data );
    }
}

class UserData_Action_Set extends AMPSystem_Data_Set {
    var $service = 'default';

    var $datatable = "userdata_actions";

    function UserData_Action( &$dbcon ) {
        $this->init( $dbcon );
    }

    function _register_criteria_dynamic() {
        $this->addCriteria( "service="
                    . $this->dbcon->qstr( $this->service ));
    }


}

?>
