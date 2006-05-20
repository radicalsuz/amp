<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_AMP_Content_Redirect extends AMPSystem_ComponentMap {
    var $heading = "Redirect";
    var $nav_name = "content";

    var $paths = array( 
        'fields' => 'AMP/Content/Redirect/Fields.xml',
        'list'   => 'AMP/Content/Redirect/List.inc.php',
        'form'   => 'AMP/Content/Redirect/Form.inc.php',
        'source' => 'AMP/Content/Redirect/Redirect.php');
    
    var $components = array( 
        'form'  => 'AMP_Content_Redirect_Form',
        'list'  => 'AMP_Content_Redirect_List',
        'source'=> 'AMP_Content_Redirect');
}

?>
