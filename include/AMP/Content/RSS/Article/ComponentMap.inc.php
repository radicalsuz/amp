<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_RSS_Article extends AMPSystem_ComponentMap {
    var $heading = "RSS Article";
    var $nav_name = "rss";
    var $_path_controller = 'AMP/Content/RSS/Article/Controller.php';
    var $_component_controller = 'RSS_Article_Controller';

    var $paths = array( 
        'list'   => 'AMP/Content/RSS/Article/List.inc.php',
        'search_fields'   => 'AMP/Content/RSS/Article/SearchFields.xml',
        'search'  => 'AMP/Form/SearchForm.inc.php',
        'source' => 'AMP/Content/RSS/Article/Article.php');
    
    var $components = array( 
        'list'  => 'RSS_Article_List',
        'search'  => 'AMPSearchForm',
        'source'=> 'RSS_Article');

    var $_allow_search = true;
    var $_action_default = 'list';
}

?>
