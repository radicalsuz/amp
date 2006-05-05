<?php

require_once( 'AMP/System/Data/Item.inc.php');

class RSS_Article extends AMPSystem_Data_Item {

    var $datatable = "px_items";
    var $name_field = "title";

    function RSS_Article ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getLinkURL( ){
        return $this->getData( 'link');
    }

    function getItemDate( ){
        return $this->getData( 'dcdate');
    }

    function getTimestamp( ){
        return $this->getData( 'timestamp');
    }
    function getSubscriptionId( ){
        return $this->getData( 'feed_id');
    }

    function getTitle( ){
        return $this->getData( 'title');
    }
    function getBody( ){
        return $this->getData( 'content');
    }
    function getFeedName( ){
        $feed_id = $this->getSubscriptionId( );
        if ( !$feed_id ) return false;
        $names_lookup = &AMPContent_Lookup::instance( 'RSS_Subscriptions');
        if ( isset( $names_lookup[ $feed_id ])) return $names_lookup[ $feed_id ];
        return $feed_id;
    }
    function getFeedNameText( ){
        $result = $this->getFeedName( );
        if ( !is_numeric( $result )) return $result;
        return $result . ' #' . strtoupper( AMP_TEXT_DELETED ) . '#';
    }

    function publish( $section_id, $class_id, $destroy_self=true ){
        $text = utf8_decode( preg_replace( "/\\n/", "<br/>", $this->getBody( )) );
        $blurb = AMP_trimText( $text, AMP_CONTENT_ARTICLE_BLURB_LENGTH_DEFAULT, false );
        $title = utf8_decode( $this->getName( ));

    }
}

?>
