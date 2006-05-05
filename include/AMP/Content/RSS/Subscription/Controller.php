<?php

require_once ( 'AMP/System/Component/Controller.php');

class RSS_Subscription_Controller extends AMP_System_Component_Controller_Map {
    var $_map;
    var $_action_default = 'list';

    function RSS_Subscription_Controller( ){
        $this->init( );
    }

    function _init_form( &$form, $read_request = true ){
        $this->_form = &$form;
        $this->notify( 'initForm' );

        if ( $read_request ) $this->_init_form_request( );
        $this->_form->Build( );
        
    }
    function _init_form_request( ){
        $request_id = $this->_form->getIdValue( );
        if ( is_array( $request_id )) return false;

        $action = $this->_form->submitted( );
        if ( !$request_id ) $this->_form->initNoId( );
        if ( $action ) $this->_action_requested = $action;
    }

    function commit_add( ){
        if ( !$this->_form->submitted( ) ){
            $this->_display->add( $this->_form );
            return true;
        }

        $data = $this->_form->getValues( );
        $result = false;

        foreach( $data as $fieldname => $value ){
            if ( isset( $_FILES[ $fieldname ] ) && isset( $_FILES[ $fieldname ]['tmp_name']) && $_FILES[ $fieldname ]['tmp_name'] ) $value = $_FILES[ $fieldname ]['tmp_name'];
            if ( !$value ) continue; 
            $feed_add_method = 'add_FOF_feed_' . $fieldname;
            if ( !method_exists( $this, $feed_add_method )) continue;
            $result = $this->$feed_add_method( $value );
        }

        $this->display_default( );
        return $result;

    }

    function add_FOF_feed_url( $url ){
        return $this->_subscribe_feeds( array( $url ));
    }

    function add_FOF_feed_opml( $opml_url ){
        return $this->add_OPML_feeds( $opml_url );
    }

    function add_OPML_feeds( $file_name ){
        $content_set = file( $file_name );
        if ( !$content_set ){
            $this->error( sprintf( AMP_TEXT_ERROR_OPEN_FAILED, $file_name ));
            return false;
        }

        $feeds = fof_opml_to_array( join( "", $content_set ));
        return $this->_subscribe_feeds( $feeds );


    }
    function add_FOF_feed_opml_file ( $opml_file_name ){
        if ( !$opml_file_name ) 
            $opml_file_name = ( isset( $_FILES[ $fieldname ]['tmp_name']) && $_FILES[ $fieldname] ['tmp_name'])
                            ? $_FILES[ $fieldname ][ 'tmp_name' ] : false;
        if ( !$opml_file_name ) return false;
        return $this->add_OPML_feeds( $opml_file_name );
    }

    function _subscribe_feeds( $feeds_array ){
        ob_start( );
        $total_subscribed = 0;
        foreach( $feeds_array as $feed ){
            $total_subscribed += fof_add_feed( $feed );
        }
        $result = ob_get_clean( );
        $this->message( $result );
        return $total_subscribed;
    }
}

?>
