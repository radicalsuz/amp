<?php

require_once('AMP/System/Copy/Copy.inc.php');
class AMPSystem_Copy_UserDataPlugin extends AMPSystem_Copy {
    var $datatable = "userdata_plugins";
    var $dependencies = array (
        'userdata_plugins_fields' => array( 'class'=>'UserDataPlugin_Field', 'child_field'=>'plugin_id' ),
        'userdata_plugins_options'=> array( 'class'=>'UserDataPlugin_Option', 'child_field'=>'plugin_id')
        );

    function AMPSystem_Copy_UserDataPlugin ( &$dbcon, $plugin_instance=null) {
        $this->init($dbcon, $plugin_instance);
    }
}


class AMPSystem_Copy_UserDataPlugin_Field extends AMPSystem_Copy {
    var $datatable = "userdata_plugins_fields";
    
    function AMPSystem_Copy_UserDataPlugin_Field(&$dbcon) {
        $this->init($dbcon);
    }
}

class AMPSystem_Copy_UserDataPlugin_Option extends AMPSystem_Copy {
    var $datatable = "userdata_plugins_options";

    function AMPSystem_Copy_UserDataPlugin_Option (&$dbcon) {
        $this->init($dbcon);
    }
}

?>
