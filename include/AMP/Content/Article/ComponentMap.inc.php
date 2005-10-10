<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_Article extends AMPSystem_ComponentMap {

    var $heading = "Site Content";

    var $nav_name = "content";

    var $paths = array(
        'search_user' => 'AMP/Content/Article/Search/User/Form.inc.php',
        'search_fields_user' => 'AMP/Content/Article/Search/User/Fields.xml',
        'search' => 'AMP/Content/Article/Search/Form.inc.php',
        'search_fields' => 'AMP/Content/Article/Search/Fields.xml',
        'source' => 'AMP/Content/Article.inc.php',
        'list' => 'AMP/Content/Article/ListForm.inc.php',
        'menu' => 'AMP/Content/Section/Menu.inc.php',
        'classlinks' => 'AMP/Content/Class/Links.inc.php' );

    var $components = array (
        'search_user' => 'ContentSearch_Form_User',
        'search' => 'ContentSearch_Form',
        'menu' => 'SectionMenu',
        'classlinks' => 'Class_Links',
        'list' => 'Article_ListForm',
        'source' => 'Article' );
}
?>
