<?php


require_once('AMP/System/Copy/Copy.inc.php');
class AMPSystem_Module_Copy extends AMPSystem_Copy {

    var $datatable = "modules";
    var $dependencies = array(
        'per_description' => array(
            'class' =>'Permission_Detail', 
            'override'=>array('name'=>array('name','description')),
            'child_field'=>'id', 
            'parent_field'=>'perid'),
        'moduletext' => array(
            'class' => 'IntroText',
            'override' => array('name'=>array('name','title','test')),
            'child_field' => 'modid')
        );

    function AMPSystem_Module_Copy (&$dbcon, $module_id = null) {
        $this->init($dbcon, $module_id);
    }


}


?>
