<?php

require_once( 'AMP/System/Component/Controller.php');

class RSS_Article_Controller extends AMP_System_Component_Controller_Map {

    function RSS_Article_Controller( ){
        $this->init( );
    }

    function commit_update( ){
        require_once( 'AMP/Content/RSS/Subscription/Subscription.php');
        $current_subscription_list = &AMPContent_Lookup::instance( 'RSS_Subscriptions');
        foreach( $current_subscription_list as $subscription_id => $subscription_name ){
            $sub = & new RSS_Subscription( AMP_Registry::getDbcon( ), $subscription_id );
            $sub->update( );
        }
    }


}

?>
