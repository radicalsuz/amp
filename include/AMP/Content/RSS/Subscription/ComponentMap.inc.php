<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_RSS_Subscription extends AMPSystem_ComponentMap {
    var $heading = "RSS Subscription";
    var $nav_name = "rss";

    var $paths = array( 
        'fields' => 'AMP/Content/RSS/Subscription/Fields.xml',
        'list'   => 'AMP/Content/RSS/Subscription/List.inc.php',
        'form'   => 'AMP/Content/RSS/Subscription/Form.inc.php',
        'source' => 'AMP/Content/RSS/Subscription/Subscription.php');
    
    var $components = array( 
        'form'  => 'RSS_Subscription_Form',
        'list'  => 'RSS_Subscription_List',
        'source'=> 'RSS_Subscription');

    var $_path_controller = 'AMP/Content/RSS/Subscription/Controller.php';
    var $_component_controller = 'RSS_Subscription_Controller';

    var $_action_default = 'list';

    var $_allow_list = AMP_PERMISSION_CONTENT_RSS_AGGREGATOR;
    var $_allow_save = AMP_PERMISSION_CONTENT_RSS_AGGREGATOR;
}

?>
