<?php

require_once( 'AMP/System/Data/Item.inc.php');

class RSS_Article extends AMPSystem_Data_Item {

    var $datatable = "px_items";
    var $name_field = "title";
    var $_exact_value_fields = array( 'feed_id');
    var $_class_name = "RSS_Article";

    function RSS_Article ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getLinkURL( ){
        return $this->getData( 'link');
    }

    function getItemDate( ){
        $date_item = $this->getData( 'dcdate');
        if ( !AMP_verifyDateValue( $date_item ))  return AMP_NULL_DATE_VALUE_DB;

        return $date_item;
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
        return false;
    }

    function getContacts( ){
        return $this->getData( 'contacts' );
    }

    function getSubtitle( ){
        return $this->getData( 'subtitle' );
    }

    function publish( $section_id, $class_id, $destroy_self=true ) {
        $text = utf8_decode( preg_replace( "/\\n/", "<br/>", $this->getBody( )) );
        $blurb = AMP_trimText( $text, AMP_CONTENT_ARTICLE_BLURB_LENGTH_DEFAULT, false );
        # this line doesnt work since upgrading adodb? ap
        #$title = $this->dbcon->qstr( utf8_decode( $this->getName( )));
        $title = utf8_decode( $this->getName( ));
        $feed_name = $this->getFeedName( );
        if ( !$section_id ) return false;
        

        $article_data = array( 
            'title' => $title, 
            'body'  => $text, 
            'shortdesc' => $blurb, 
            'uselink'   => ( !AMP_CONTENT_RSS_FULLTEXT ), 
            'linkover'  => ( !AMP_CONTENT_RSS_FULLTEXT ), 
            'link'  => $this->getLinkURL( ), 
            'subtitle' => $this->getSubtitle( ),
            'source'    => $feed_name, 
            'sourceurl' => AMP_validate_url( $this->getLinkURL( )), 
            'type'  => $section_id, 
            'class' => $class_id, 
            'date'  => $this->getItemDate( ),
            'publish' => AMP_CONTENT_STATUS_LIVE, 
            'enteredby' => AMP_SYSTEM_USER_ID, 
            'updatedby' => AMP_SYSTEM_USER_ID, 
            'contact' => $this->getContacts( )  
            );


        require_once( 'AMP/Content/Article.inc.php');
        $article = &new Article( $this->dbcon );
        $article->setDefaults( );
        $article->setData( $article_data );
        if ( !$article->save( )) return false;

        if ( $destroy_self ) $this->delete( );

        return true;

    }

    function _sort_default( &$item_set ){
        return $this->sort( $item_set, 'timestamp', AMP_SORT_DESC);
    }

    function makeCriteriaTimestamp( $timestamp_value ) {
        $dbcon = &AMP_Registry::getDbcon( );
        return ( 'timestamp > ' . $dbcon->qstr( date( 'Y-m-d h:i:s', $timestamp_value )));

    }

}

?>
