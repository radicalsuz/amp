<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'AMP/Content/RssFeed/RssFeed.php');

class RssFeedSet extends AMPSystem_Data_Set {
    var $datatable = 'rssfeed';
    var $sort = array( "title");

    function RssFeedSet ( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
