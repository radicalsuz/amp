<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'AMP/Content/RSS/Subscription/Subscription.php');

class RSS_SubscriptionSet extends AMPSystem_Data_Set {
    var $datatable = 'px_feeds';
    var $sort = array( "title");

    function RSS_SubscriptionSet ( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
