<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/RSS/Feed/ComponentMap.inc.php');

class RSS_Feed_Form extends AMPSystem_Form_XML {

    var $name_field = 'title';

    function RSS_Feed_Form( ) {
        $name = 'rssfeed';
        $this->init( $name );
    }

    function setDynamicValues( ){
        /*auto scaffolded items here  auto scaffold items end */
    }
}
?>
