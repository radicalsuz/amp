<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/RSS/Subscription/ComponentMap.inc.php');
require_once( 'FeedOnFeeds/init_adodb.php');

class RSS_Subscription_Form extends AMPSystem_Form_XML {

    var $name_field = 'title';
    var $submit_button = array( 
        'submitAction' => array(
            'type' => 'group',
            'elements'=> array(
                'add' => array(
                    'type' => 'submit',
                    'label' => 'Add Feed')
            )));

    function RSS_Subscription_Form( ) {
        $name = 'RSS_Subscriptions';
        $this->init( $name, 'POST', AMP_SYSTEM_URL_RSS_SUBSCRIPTION );
    }

    function _manageUpload( $data, $fieldname ){
        return false;
    }

    /*
    function _formFooter( ){
        require_once( 'AMP/Content/RSS/Subscription/List.inc.php');
        require_once( 'AMP/Content/Display/HTML.inc.php');
        $renderer = &new AMPDisplay_HTML( );
        $list = &new RSS_Subscription_List( AMP_Registry::getDbcon( ));
        return  $renderer->inDiv( 'Subscribed Feeds', array( 'class' => 'banner')) 
                . $list->execute( );
    }

    function setDynamicValues( ){
        
    }
    */

}
?>
