<?php
require_once ( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_Region extends AMPSystem_ComponentMap {

    var $heading = "Region";
    var $nav_name = "contenttools";
    var $paths = array(
        'form' => 'AMP/System/Region/Form.inc.php',
        'list' => 'AMP/System/Region/List.inc.php',
        'source' => 'AMP/System/Region.inc.php' );
    var $components = array(
        'form' => 'AMPSystem_Region_Form',
        'list' => 'AMPSystem_Region_List',
        'source' => 'AMPSystem_Region' );

}

?>
