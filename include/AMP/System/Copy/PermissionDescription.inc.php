<?php

require_once('AMP/System/Copy/Copy.inc.php');
class AMPSystem_Copy_PermissionDescription extends AMPSystem_Copy {

    var $datatable = "per_description";
    var $dependencies = array(
        'permission' => array(
            'class' =>'PermissionSetting', 
            'child_field'=>'perid')
        );

    function AMPSystem_Copy_PermissionDescription($dbcon, $perid=null) {
        $this->init($dbcon, $perid);
    }

}

class AMPSystem_Copy_PermissionSetting extends AMPSystem_Copy {

    var $datatable = "permission";

    function AMPSystem_Copy_PermissionSetting( &$dbcon, $perid=null) {
        $this->init($dbcon, $perid);
    }
}


?>
