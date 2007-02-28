<?php

require_once( 'AMP/System/ComponentMap.inc.php');
require_once( 'AMP/System/Permission/Observer/Section.php');

class ComponentMap_RSS_Feed extends AMPSystem_ComponentMap {
    var $heading = "RSS Feed";
    var $nav_name = "rss";

    var $paths = array( 
        'fields' => 'AMP/Content/RSS/Feed/Fields.xml',
        'list'   => 'AMP/Content/RSS/Feed/List.inc.php',
        'form'   => 'AMP/Content/RSS/Feed/Form.inc.php',
        'source' => 'AMP/Content/RSS/Feed.inc.php');
    
    var $components = array( 
        'form'  => 'RSS_Feed_Form',
        'list'  => 'RSS_Feed_List',
        'source'=> 'AMPContent_RssFeed');

    var $_observers = array( 'AMP_System_Permission_Observer_Section');

    var $_allow_list = AMP_PERMISSION_CONTENT_RSS_ACCESS;
    var $_allow_publish = AMP_PERMISSION_CONTENT_RSS_PUBLISH;
    var $_allow_save = AMP_PERMISSION_CONTENT_RSS_ACCESS;

}

?>
