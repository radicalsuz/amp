<?php

require_once( 'AMP/System/List.inc.php' );
require_once( 'AMP/Display/System/List.php' );
require_once( 'AMP/Content/RSS/Feed.inc.php' );

class RSS_Feed_List extends AMP_Display_System_List {
    var $name = "RssFeed";
    var $col_headers = array( 
        'Title' => 'title',
        'ID'    => 'id');
    var $editlink = 'rssfeed.php';
    var $name_field = 'title';
    var $_source_object = 'AMPContent_RSSFeed';
    var $_actions = array( 'delete');
    var $link_list_preview = AMP_CONTENT_URL_RSSFEED_LIST;

    function RSS_Feed_List( $source, $criteria = array( ) ) {
        $this->__construct( $source, $criteria );
        //$this->init( $this->_init_source( $dbcon ) );
    }

}
?>
