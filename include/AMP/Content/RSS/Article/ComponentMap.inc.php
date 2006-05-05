<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_RSS_Article extends AMPSystem_ComponentMap {
    var $heading = "RSS Article";
    var $nav_name = "rss";
    var $_component_controller = 'AMP_System_Component_Controller_Map';

    var $paths = array( 
        'list'   => 'AMP/Content/RSS/Article/List.inc.php',
        'source' => 'AMP/Content/RSS/Article/Article.php');
    
    var $components = array( 
        'list'  => 'RSS_Article_List',
        'source'=> 'RSS_Article');
}

?>
