<?php

require_once( 'AMP/System/List/Request.inc.php');

class Article_List_Request extends AMP_System_List_Request {

    function Article_List_Request( &$source ) {
        $this->__construct( $source );
    }

    function getPerformedAction( ){
        if ( $this->_committed_action == 'request_revision') {
            return 'revision request';
        }
        return $this->_committed_action;
    }

}


?>
