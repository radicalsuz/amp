<?php
require_once('AMP/System/Copy/Copy.inc.php');

class AMPSystem_UserData_Module_Copy extends AMPSystem_Copy {
    var $datatable = "userdata_fields";
    var $dependencies = array (
        'modidinput'=>array('class'=>'IntroText', 'child_field'=>'id', 'parent_field'=>'modidinput',
                            'override'=>array ('name'=>array('name','title','test'))),
        'modidresponse'=>array('class'=>'IntroText', 'child_field'=>'id', 'parent_field'=>'modidresponse',
                            'override'=>array ('name'=>array('name','title','test'))),
        'userdata_plugins'=> array( 'class'=>'UserData_Plugin', 'child_field'=>'instance_id'),
        'modules'=>array( 'class'=>'Module', 'child_field'=>'userdatamodid', 
                            'override'=> array('name'=>array('name', 'navhtml'), 
                                               'id'=>array('navhtml','file')) )
        );

    function AMPSystem_UserData_Module_Copy (&$dbcon, $modin=null) {
        $this->init($dbcon, $modin);
        //turn on the paginator to prevent copies from being made twice of the
        //same item
        $this->PaginateOn();
    }
}
?>
