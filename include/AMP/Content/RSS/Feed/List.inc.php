<?php

require_once( 'AMP/System/List.inc.php' );
require_once( 'AMP/Content/RSS/Feed.inc.php' );

class RSS_Feed_List extends AMPSystem_List {
    var $name = "RssFeed";
    var $col_headers = array( 
        'Title' => 'title',
        'ID'    => 'id');
    var $editlink = 'rssfeed.php';
    var $name_field = 'title';
    var $_source_object = 'AMPContent_RSSFeed';

    function RSS_Feed_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
    }
}
?>
