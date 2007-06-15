<?php

require_once( 'AMP/System/List/Request.inc.php');

class AMP_User_List_Request extends AMP_System_List_Request {

    function AMP_System_List_Request( &$source ){
        $this->__construct( $source );
    }

    function subscribe( &$target_set, $args = null ) {
        $lists = AMP_lookup( 'lists');
        if ( !( isset( $args['list_id']) && $args['list_id'] && isset( $lists[$args['list_id']]))) {
            $flash = &AMP_System_Flash::instance( );
            $flash->add_error( sprintf( AMP_TEXT_ERROR_NO_SELECTION, AMP_TEXT_LIST ) );
            return false;
        }

        $emails = array( );
        foreach( $target_set as $target ) {
            $emails[] = $target->getEmail( );
        }

        return AMP_subscribe_to_list( $emails, $args['list_id']);

    }


}


?>
