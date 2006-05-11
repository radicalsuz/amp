<?php

require_once( 'AMP/System/Data/Item.inc.php');

class RSS_Subscription extends AMPSystem_Data_Item {

    var $datatable = "px_feeds";
    var $name_field = "title";

    function RSS_Subscription ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function _afterSave( ){
        $data = $this->getData( );
    }

    function getURL( ){
        return $this->getData( 'url');
    }

    function getTitle( ){
        return $this->getName();
    }

    function getBlurb( ){
        return $this->getData( 'description');
    }

    function getLink( ){
        return $this->getData( 'link');
    }

    function update( ){
        require_once( 'FeedOnFeeds/init_adodb.php');
        if ( !( $url = $this->getURL( ))) return false;
        $count = fof_update_feed( $this->getURL( ));
        if ( !$count ) $count = '0';
        $flash = &AMP_System_Flash::instance( );
        $flash->add_message( sprintf( AMP_TEXT_CONTENT_RSS_ITEMS_ADDED, $count, $this->getName( ) ));

        return true;
    }
}

?>
