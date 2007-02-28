<?php

require_once( 'AMP/System/ComponentMap.inc.php');
require_once( 'AMP/System/Permission/Observer/Section.php');

class ComponentMap_Quote extends AMPSystem_ComponentMap {
    var $heading = "Quote";
    var $nav_name = "content";

    var $paths = array( 
        'fields' => 'AMP/Content/Quote/Fields.xml',
        'list'   => 'AMP/Content/Quote/List.inc.php',
        'form'   => 'AMP/Content/Quote/Form.inc.php',
        'source' => 'AMP/Content/Quote/Quote.php');
    
    var $components = array( 
        'form'  => 'Quote_Form',
        'list'  => 'Quote_List',
        'source'=> 'Quote');

    var $_observers = array( 'AMP_System_Permission_Observer_Section');

    var $_allow_list = AMP_PERMISSION_QUOTES_ACCESS ;
    var $_allow_edit = AMP_PERMISSION_QUOTES_ACCESS ;
    var $_allow_save = AMP_PERMISSION_QUOTES_ACCESS;
    var $_allow_publish = AMP_PERMISSION_QUOTES_ACCESS;
    var $_allow_unpublish = AMP_PERMISSION_QUOTES_ACCESS;
    var $_allow_delete = AMP_PERMISSION_QUOTES_ACCESS;
}

?>
