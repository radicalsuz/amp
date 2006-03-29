<?php

require_once( 'AMP/System/Data/Item.inc.php');

class ArticleComment extends AMPSystem_Data_Item {

    var $datatable = "comments";
    var $name_field = "comment";

    function ArticleComment ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getTimestamp( ){
        return strtotime( $this->getData( 'date'));
    }

    function getDate( ){
        return $this->getData( 'date');
    }

    function _sort_default( &$item_set ){
        return $this->sort( $item_set, 'timestamp', AMP_SORT_DESC );
    }

    function publish( ){
        return
    }
    function isLive() {
        return ($this->getData('publish')==AMP_CONTENT_STATUS_LIVE);
    }

    function getPublish( ){
        return $this->isLive( ) ;

    }
}

?>
