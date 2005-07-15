<?php

require_once('AMP/System/Copy/Copy.inc.php');
class AMPSystem_UserData_Plugin_Copy extends AMPSystem_Copy {
    var $datatable = "userdata_plugins";
    var $dependencies = array (
        'userdata_plugins_fields' => array( 'class'=>'UserData_Plugin_Field', 'child_field'=>'plugin_id' ),
        'userdata_plugins_options'=> array( 'class'=>'UserData_Plugin_Option', 'child_field'=>'plugin_id')
        );

    function AMPSystem_UserData_Plugin_Copy ( &$dbcon, $plugin_instance=null) {
        $this->init($dbcon, $plugin_instance);
    }
}


class AMPSystem_UserData_Plugin_Field_Copy extends AMPSystem_Copy {
    var $datatable = "userdata_plugins_fields";
    
    function AMPSystem_UserData_Plugin_Field_Copy (&$dbcon) {
        $this->init($dbcon);
    }
}

class AMPSystem_UserData_Plugin_Option_Copy extends AMPSystem_Copy {
    var $datatable = "userdata_plugins_options";

    function AMPSystem_UserData_Plugin_Option_Copy (&$dbcon) {
        $this->init($dbcon);
    }
}

?>
