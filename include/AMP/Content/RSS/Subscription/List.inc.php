<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/RSS/Subscription/Subscription.php');

class RSS_Subscription_List extends AMP_System_List_Form {
    var $name = "RSS_Subscription";
    var $col_headers = array( 
        'Name'  => 'Title',
        'URL'   => 'URL',
        'ID'    => 'id' );
    var $editlink = 'rss_subscription.php';
    var $name_field = 'title';
    var $_source_object = 'RSS_Subscription';
    var $_source_criteria = '( isnull( service ) or service="Content")';
    var $_actions = array( 'update', 'delete');
    var $_observers_source = array( 'AMP_System_List_Observer' );
    var $_url_add = 'rss_subscription.php?action=add';

    function RSS_Subscription_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
        $this->suppressEditColumn( );
    }

}
?>
