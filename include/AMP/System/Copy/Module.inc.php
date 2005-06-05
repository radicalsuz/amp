<?php


require_once('AMP/System/Copy/Copy.inc.php');
class AMPSystem_Copy_Module extends AMPSystem_Copy {

    var $datatable = "modules";
    var $dependencies = array(
        'per_description' => array(
            'class' =>'PermissionDescription', 
            'override'=>array('name'=>array('name','description')),
            'child_field'=>'id', 
            'parent_field'=>'perid'),
        'moduletext' => array(
            'class' => 'HeaderText',
            'override' => array('name'=>array('name','title','test')),
            'child_field' => 'modid')
        );

    function AMPSystem_Copy_Module(&$dbcon, $module_id = null) {
        $this->init($dbcon, $module_id);
    }


}


?>
