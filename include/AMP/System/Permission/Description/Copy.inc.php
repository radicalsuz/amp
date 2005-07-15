<?php

require_once('AMP/System/Copy/Copy.inc.php');
class AMPSystem_Permission_Description_Copy extends AMPSystem_Copy {

    var $datatable = "per_description";
    var $dependencies = array(
        'permission' => array(
            'class' =>'Permission_Setting', 
            'child_field'=>'perid')
        );

    function AMPSystem_Permission_Description_Copy ($dbcon, $perid=null) {
        $this->init($dbcon, $perid);
    }

}

class AMPSystem_Permission_Setting_Copy extends AMPSystem_Copy {

    var $datatable = "permission";

    function AMPSystem_Permission_Setting_Copy ( &$dbcon, $perid=null) {
        $this->init($dbcon, $perid);
    }
}


?>
