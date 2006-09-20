<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_AMP_Content_Tag extends AMPSystem_ComponentMap {
    var $heading = AMP_TEXT_TAG;
    var $nav_name = "content";
    var $_action_default = 'list';

    var $paths = array( 
        'fields' => 'AMP/Content/Tag/Fields.xml',
        'list'   => 'AMP/Content/Tag/List.php',
        'form'   => 'AMP/Content/Tag/Form.php',
        'source' => 'AMP/Content/Tag/Tag.php');
    
    var $components = array( 
        'form'  => 'AMP_Content_Tag_Form',
        'list'  => 'AMP_Content_Tag_List',
        'source'=> 'AMP_Content_Tag' );

}

?>
