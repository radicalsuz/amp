<?php
require_once ( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_Article_Frontpage extends AMPSystem_ComponentMap {

    var $heading = "Front Page Content";
    var $nav_name = "content";
    var $_action_default = 'list';

    var $paths = array( 
        'fields' => 'AMP/Content/Article/Frontpage/Fields.xml',
        'form'          => 'AMP/Content/Article/Frontpage/Form.inc.php',
        'source' => 'AMP/Content/Article.inc.php',
        'list' => 'AMP/Content/Article/Frontpage/List.inc.php',
    );

    var $components = array (
        'search_user' => 'ContentSearch_Form_User',
        'menu'        => 'SectionMenu',
        'list'        => 'Article_Frontpage_List',
        'form'        => 'Article_Frontpage_Form',
        'source'      => 'Article' 
        );
}
?>
