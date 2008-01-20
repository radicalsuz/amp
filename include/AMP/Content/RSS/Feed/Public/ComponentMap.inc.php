<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_RSS_Feed_Public extends AMPSystem_ComponentMap {
    var $_action_default = 'list';
    var $_path_controller = 'AMP/System/Component/Controller/Public.php';
    var $_component_controller = 'AMP_System_Component_Controller_Public';
    var $_public_page_id_list = AMP_CONTENT_PUBLICPAGE_ID_RSS_FEED_LIST;

    var $paths = array( 
        'list'   => 'AMP/Content/RSS/Feed/Public/List.php',
        'source' => 'AMP/Content/RSS/Feed.inc.php',
        );
    
    var $components = array( 
        'list'  => 'RSS_Feed_Public_List',
        'source'=> 'AMPContent_RSSFeed',
        );

    var $_allow_new = false;
    var $_allow_delete = false;
    var $_allow_add = false;
}

?>
