<?php

require_once( 'AMP/System/Data/Item.inc.php');

class RssFeed extends AMPSystem_Data_Item {

    var $datatable = "rssfeed";
    var $name_field = "title";

    function RssFeed ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }
}

?>
